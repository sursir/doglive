<?php

error_reporting(E_ALL);

/* Data can be send to coroutines using `$coroutine->send($data)`. The sent data will then
 * be the result of the `yield` expression. Thus it can be received using a code like
 * `$data = yield;`.
 */

/* What we're building in this script is a coroutine-based streaming XML parser. The PHP
 * extension for parsing streamed XML is xml_parser. It is used by defining a set of
 * callback functions for various events (like start tag, end tag, content).
 *
 * This event model makes the parsing process very complicated, because you basically
 * have to implement your own state machine (which is a lot of boilerplate code the
 * more complicated the XML gets).
 *
 * To solve this problem, we build a wrapper (the following function), which redirects
 * the events to a coroutine ($target). This is done simply using
 * `$target->send([$eventName, $data])`.
 */
function streamingXMLParser($target) {
    $xmlParser = xml_parser_create();
    xml_set_element_handler(
        $xmlParser,
        function ($xmlParser, $name, array $attributes) use ($target) {
            $target->send(['start', [$name, $attributes]]);
        },
        function ($xmlParser, $name) use ($target) {
            $target->send(['end', $name]);
        }
    );
    xml_set_character_data_handler(
        $xmlParser,
        function ($xmlParser, $text) use ($target) {
            $target->send(['text', $text]);
        }
    );

    while ($data = yield) {
        if (!xml_parse($xmlParser, $data)) {
            throw new Exception(sprintf(
                'XML error "%s" on line %d',
                xml_error_string(xml_get_error_code($xmlParser)),
                xml_get_current_line_number($xmlParser)
            ));
        }
    }

    xml_parser_free($xmlParser);
}

/* Inside the target coroutine the actual parsing happens. The events are received
 * using `list($event, $data) = yield`. The main advantage that coroutines bring
 * here is that you can fetch the events in nested loops. This way you are implicitly
 * building a state machine (but the state is managed by PHP, not you!)
 *
 * This particular coroutine parses bus location data (for samples scroll down). The
 * result is passed to another $target coroutine.
 */
function busXMLParser($target) {
    while (true) {
        list($event, $data) = yield;
        if ($event == 'start' && $data[0] == 'BUS') {
            $dict = [];
            $content = '';
            while (true) {
                list($event, $data) = yield;
                if ($event == 'start') {
                    $content = '';
                } elseif ($event == 'text') {
                    $content .= $data;
                } elseif ($event == 'end') {
                    if ($data == 'BUS') {
                        $target->send($dict);
                        break;
                    }

                    $dict[strtolower($data)] = $content;
                }
            }
        }
    }
}

/* This coroutine prints out the info it receives from the bus XML parser. */
function busLocationPrinter() {
    while (true) {
        $data = yield;
        echo "Bus $data[id] is currently at $data[latitude]/$data[longitude]\n";
    }
}

/* Here we are building up a coroutine pipeline. You should read this as:
 * The streaming XML parser is passing data to the bus XML parser, which
 * is passing data to the bus location printer.
 */
$parser = streamingXMLParser(busXMLParser(busLocationPrinter()));

/* I don't have access to a real bus location API, so I'll just stream some
 * fictional sample data */
$parser->send('<?xml version="1.0"?><buses>');
while (true) {
    sleep(1);
    $parser->send(sprintf(
        '<bus><id>%d</id><latitude>%f</latitude><longitude>%f</longitude></bus>',
        mt_rand(1, 1000), lcg_value(), lcg_value()
    ));
}

/* If your head is buzzing now, that's a good thing :P */
