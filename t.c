#include <stdio.h>
#include <string.h>
#include <stdlib.h>

#define MAX_ROW 5
#define MAX_COL 5

int maze[MAX_ROW][MAX_COL] = {
    0, 1, 0, 0, 0,
    0, 1, 0, 1, 0,
    0, 0, 0, 0, 0,
    0, 1, 1, 1, 0,
    0, 0, 0, 1, 0
};

int top = 0;
struct point {
    int row, col;
} stack[512];

int push(struct point p)
{
    stack[top++] = p;
}
struct point pop(void)
{
    return stack[--top];
}
int is_empty(void)
{
    return top == 0;
}

struct point predecessor[MAX_ROW][MAX_COL] = {
    {{-1, -1}, {-1, -1}, {-1, -1}, {-1, -1}, {-1, -1}},
    {{-1, -1}, {-1, -1}, {-1, -1}, {-1, -1}, {-1, -1}},
    {{-1, -1}, {-1, -1}, {-1, -1}, {-1, -1}, {-1, -1}},
    {{-1, -1}, {-1, -1}, {-1, -1}, {-1, -1}, {-1, -1}},
    {{-1, -1}, {-1, -1}, {-1, -1}, {-1, -1}, {-1, -1}}
};

int visit(int row, int col, struct point pre)
{
    struct point pt = {row, col};
    // 已访问
    maze[row][col] = 2;
    // 前驱
    predecessor[row][col] = pre;
    // 压栈
    push(pt);
}

// 不为空的时候
while (! is_empty()) {

}
