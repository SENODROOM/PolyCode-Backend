#include <stdio.h>
#include <stdlib.h>
#include <limits.h>
#include <stdbool.h>

#define MAX_VERTICES 100
#define INF INT_MAX

// =============================================================================
// GRAPH DATA STRUCTURES
// =============================================================================

// Adjacency Matrix representation
typedef struct {
    int numVertices;
    int adjMatrix[MAX_VERTICES][MAX_VERTICES];
} GraphMatrix;

// Adjacency List representation
typedef struct Node {
    int vertex;
    int weight;
    struct Node *next;
} Node;

typedef struct {
    int numVertices;
    Node *adjLists[MAX_VERTICES];
} GraphList;

// =============================================================================
// GRAPH CREATION AND MANIPULATION
// =============================================================================

// Initialize graph (matrix)
void initGraphMatrix(GraphMatrix *graph, int numVertices) {
    graph->numVertices = numVertices;
    for (int i = 0; i < numVertices; i++) {
        for (int j = 0; j < numVertices; j++) {
            graph->adjMatrix[i][j] = (i == j) ? 0 : INF;
        }
    }
}

// Add edge to matrix graph
void addEdgeMatrix(GraphMatrix *graph, int src, int dest, int weight, bool directed) {
    graph->adjMatrix[src][dest] = weight;
    if (!directed) {
        graph->adjMatrix[dest][src] = weight;
    }
}

// Initialize graph (list)
void initGraphList(GraphList *graph, int numVertices) {
    graph->numVertices = numVertices;
    for (int i = 0; i < numVertices; i++) {
        graph->adjLists[i] = NULL;
    }
}

// Add edge to list graph
void addEdgeList(GraphList *graph, int src, int dest, int weight, bool directed) {
    Node *newNode = (Node*)malloc(sizeof(Node));
    newNode->vertex = dest;
    newNode->weight = weight;
    newNode->next = graph->adjLists[src];
    graph->adjLists[src] = newNode;
    
    if (!directed) {
        newNode = (Node*)malloc(sizeof(Node));
        newNode->vertex = src;
        newNode->weight = weight;
        newNode->next = graph->adjLists[dest];
        graph->adjLists[dest] = newNode;
    }
}

// =============================================================================
// DEPTH-FIRST SEARCH (DFS)
// =============================================================================

bool visited[MAX_VERTICES];

void dfsMatrix(GraphMatrix *graph, int vertex) {
    visited[vertex] = true;
    printf("%d ", vertex);
    
    for (int i = 0; i < graph->numVertices; i++) {
        if (graph->adjMatrix[vertex][i] != INF && !visited[i]) {
            dfsMatrix(graph, i);
        }
    }
}

void dfsList(GraphList *graph, int vertex) {
    visited[vertex] = true;
    printf("%d ", vertex);
    
    Node *temp = graph->adjLists[vertex];
    while (temp != NULL) {
        int adjVertex = temp->vertex;
        if (!visited[adjVertex]) {
            dfsList(graph, adjVertex);
        }
        temp = temp->next;
    }
}

// =============================================================================
// BREADTH-FIRST SEARCH (BFS)
// =============================================================================

typedef struct {
    int items[MAX_VERTICES];
    int front;
    int rear;
} Queue;

void initQueue(Queue *q) {
    q->front = -1;
    q->rear = -1;
}

bool isEmpty(Queue *q) {
    return q->rear == -1;
}

void enqueue(Queue *q, int value) {
    if (q->rear == MAX_VERTICES - 1) return;
    
    if (q->front == -1) {
        q->front = 0;
    }
    q->rear++;
    q->items[q->rear] = value;
}

int dequeue(Queue *q) {
    if (isEmpty(q)) return -1;
    
    int item = q->items[q->front];
    q->front++;
    
    if (q->front > q->rear) {
        q->front = q->rear = -1;
    }
    
    return item;
}

void bfsMatrix(GraphMatrix *graph, int startVertex) {
    for (int i = 0; i < graph->numVertices; i++) {
        visited[i] = false;
    }
    
    Queue q;
    initQueue(&q);
    
    visited[startVertex] = true;
    enqueue(&q, startVertex);
    
    while (!isEmpty(&q)) {
        int currentVertex = dequeue(&q);
        printf("%d ", currentVertex);
        
        for (int i = 0; i < graph->numVertices; i++) {
            if (graph->adjMatrix[currentVertex][i] != INF && !visited[i]) {
                visited[i] = true;
                enqueue(&q, i);
            }
        }
    }
}

void bfsList(GraphList *graph, int startVertex) {
    for (int i = 0; i < graph->numVertices; i++) {
        visited[i] = false;
    }
    
    Queue q;
    initQueue(&q);
    
    visited[startVertex] = true;
    enqueue(&q, startVertex);
    
    while (!isEmpty(&q)) {
        int currentVertex = dequeue(&q);
        printf("%d ", currentVertex);
        
        Node *temp = graph->adjLists[currentVertex];
        while (temp != NULL) {
            int adjVertex = temp->vertex;
            if (!visited[adjVertex]) {
                visited[adjVertex] = true;
                enqueue(&q, adjVertex);
            }
            temp = temp->next;
        }
    }
}

// =============================================================================
// DIJKSTRA'S ALGORITHM
// =============================================================================

int distances[MAX_VERTICES];
int previous[MAX_VERTICES];

int minDistance(int distances[], bool visited[], int numVertices) {
    int min = INF;
    int minIndex = -1;
    
    for (int v = 0; v < numVertices; v++) {
        if (!visited[v] && distances[v] <= min) {
            min = distances[v];
            minIndex = v;
        }
    }
    
    return minIndex;
}

void dijkstra(GraphMatrix *graph, int startVertex) {
    for (int i = 0; i < graph->numVertices; i++) {
        distances[i] = INF;
        previous[i] = -1;
        visited[i] = false;
    }
    
    distances[startVertex] = 0;
    
    for (int count = 0; count < graph->numVertices - 1; count++) {
        int u = minDistance(distances, visited, graph->numVertices);
        
        if (u == -1) break;
        
        visited[u] = true;
        
        for (int v = 0; v < graph->numVertices; v++) {
            if (!visited[v] && graph->adjMatrix[u][v] != INF && 
                distances[u] != INF && distances[u] + graph->adjMatrix[u][v] < distances[v]) {
                distances[v] = distances[u] + graph->adjMatrix[u][v];
                previous[v] = u;
            }
        }
    }
}

void printPath(int parent[], int j) {
    if (parent[j] == -1) {
        printf("%d", j);
        return;
    }
    
    printPath(parent, parent[j]);
    printf(" -> %d", j);
}

void printDijkstraResults(int startVertex, int numVertices) {
    printf("Vertex\tDistance\tPath\n");
    for (int i = 0; i < numVertices; i++) {
        if (distances[i] == INF) {
            printf("%d\tINF\t\tNo path\n", i);
        } else {
            printf("%d\t%d\t\t", i, distances[i]);
            printPath(previous, i);
            printf("\n");
        }
    }
}

// =============================================================================
// BELLMAN-FORD ALGORITHM
// =============================================================================

bool bellmanFord(GraphMatrix *graph, int startVertex) {
    for (int i = 0; i < graph->numVertices; i++) {
        distances[i] = INF;
        previous[i] = -1;
    }
    distances[startVertex] = 0;
    
    // Relax edges V-1 times
    for (int i = 1; i <= graph->numVertices - 1; i++) {
        for (int u = 0; u < graph->numVertices; u++) {
            for (int v = 0; v < graph->numVertices; v++) {
                if (graph->adjMatrix[u][v] != INF && distances[u] != INF &&
                    distances[u] + graph->adjMatrix[u][v] < distances[v]) {
                    distances[v] = distances[u] + graph->adjMatrix[u][v];
                    previous[v] = u;
                }
            }
        }
    }
    
    // Check for negative weight cycles
    for (int u = 0; u < graph->numVertices; u++) {
        for (int v = 0; v < graph->numVertices; v++) {
            if (graph->adjMatrix[u][v] != INF && distances[u] != INF &&
                distances[u] + graph->adjMatrix[u][v] < distances[v]) {
                return false; // Negative cycle detected
            }
        }
    }
    
    return true;
}

// =============================================================================
// FLOYD-WARSHALL ALGORITHM
// =============================================================================

int dist[MAX_VERTICES][MAX_VERTICES];

void floydWarshall(GraphMatrix *graph) {
    // Initialize distance matrix
    for (int i = 0; i < graph->numVertices; i++) {
        for (int j = 0; j < graph->numVertices; j++) {
            dist[i][j] = graph->adjMatrix[i][j];
        }
    }
    
    // Find shortest paths
    for (int k = 0; k < graph->numVertices; k++) {
        for (int i = 0; i < graph->numVertices; i++) {
            for (int j = 0; j < graph->numVertices; j++) {
                if (dist[i][k] != INF && dist[k][j] != INF && 
                    dist[i][k] + dist[k][j] < dist[i][j]) {
                    dist[i][j] = dist[i][k] + dist[k][j];
                }
            }
        }
    }
}

void printFloydWarshallResults(int numVertices) {
    printf("All-pairs shortest paths:\n");
    for (int i = 0; i < numVertices; i++) {
        for (int j = 0; j < numVertices; j++) {
            if (dist[i][j] == INF) {
                printf("INF ");
            } else {
                printf("%3d ", dist[i][j]);
            }
        }
        printf("\n");
    }
}

// =============================================================================
// MINIMUM SPANNING TREE - PRIM'S ALGORITHM
// =============================================================================

int key[MAX_VERTICES];
int mstSet[MAX_VERTICES];

int minKey(int key[], bool mstSet[], int numVertices) {
    int min = INF;
    int minIndex = -1;
    
    for (int v = 0; v < numVertices; v++) {
        if (!mstSet[v] && key[v] < min) {
            min = key[v];
            minIndex = v;
        }
    }
    
    return minIndex;
}

void primMST(GraphMatrix *graph) {
    for (int i = 0; i < graph->numVertices; i++) {
        key[i] = INF;
        mstSet[i] = false;
    }
    
    key[0] = 0; // Start with first vertex
    
    for (int count = 0; count < graph->numVertices - 1; count++) {
        int u = minKey(key, (bool*)mstSet, graph->numVertices);
        mstSet[u] = true;
        
        for (int v = 0; v < graph->numVertices; v++) {
            if (graph->adjMatrix[u][v] != INF && !mstSet[v] && 
                graph->adjMatrix[u][v] < key[v]) {
                key[v] = graph->adjMatrix[u][v];
            }
        }
    }
    
    // Print MST
    printf("Edge \tWeight\n");
    for (int i = 1; i < graph->numVertices; i++) {
        printf("%d - %d \t%d\n", previous[i], i, key[i]);
    }
}

// =============================================================================
// TOPOLOGICAL SORT
// =============================================================================

typedef struct {
    int items[MAX_VERTICES];
    int top;
} Stack;

void initStack(Stack *s) {
    s->top = -1;
}

bool isStackEmpty(Stack *s) {
    return s->top == -1;
}

void push(Stack *s, int value) {
    s->top++;
    s->items[s->top] = value;
}

int pop(Stack *s) {
    if (isStackEmpty(s)) return -1;
    
    int item = s->items[s->top];
    s->top--;
    return item;
}

void topologicalSortUtil(GraphList *graph, int vertex, Stack *stack) {
    visited[vertex] = true;
    
    Node *temp = graph->adjLists[vertex];
    while (temp != NULL) {
        int adjVertex = temp->vertex;
        if (!visited[adjVertex]) {
            topologicalSortUtil(graph, adjVertex, stack);
        }
        temp = temp->next;
    }
    
    push(stack, vertex);
}

void topologicalSort(GraphList *graph) {
    Stack stack;
    initStack(&stack);
    
    for (int i = 0; i < graph->numVertices; i++) {
        visited[i] = false;
    }
    
    for (int i = 0; i < graph->numVertices; i++) {
        if (!visited[i]) {
            topologicalSortUtil(graph, i, &stack);
        }
    }
    
    printf("Topological sort: ");
    while (!isStackEmpty(&stack)) {
        printf("%d ", pop(&stack));
    }
    printf("\n");
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void createSampleGraph(GraphMatrix *matrixGraph, GraphList *listGraph) {
    // Create a sample graph
    int numVertices = 6;
    
    initGraphMatrix(matrixGraph, numVertices);
    initGraphList(listGraph, numVertices);
    
    // Add edges (undirected for simplicity)
    addEdgeMatrix(matrixGraph, 0, 1, 4, false);
    addEdgeMatrix(matrixGraph, 0, 2, 4, false);
    addEdgeMatrix(matrixGraph, 1, 2, 2, false);
    addEdgeMatrix(matrixGraph, 1, 3, 5, false);
    addEdgeMatrix(matrixGraph, 2, 3, 8, false);
    addEdgeMatrix(matrixGraph, 2, 4, 10, false);
    addEdgeMatrix(matrixGraph, 3, 4, 2, false);
    addEdgeMatrix(matrixGraph, 3, 5, 6, false);
    addEdgeMatrix(matrixGraph, 4, 5, 3, false);
    
    // Same edges for list representation
    addEdgeList(listGraph, 0, 1, 4, false);
    addEdgeList(listGraph, 0, 2, 4, false);
    addEdgeList(listGraph, 1, 2, 2, false);
    addEdgeList(listGraph, 1, 3, 5, false);
    addEdgeList(listGraph, 2, 3, 8, false);
    addEdgeList(listGraph, 2, 4, 10, false);
    addEdgeList(listGraph, 3, 4, 2, false);
    addEdgeList(listGraph, 3, 5, 6, false);
    addEdgeList(listGraph, 4, 5, 3, false);
}

void demonstrateDFS() {
    printf("=== DEPTH-FIRST SEARCH ===\n");
    
    GraphMatrix matrixGraph;
    GraphList listGraph;
    createSampleGraph(&matrixGraph, &listGraph);
    
    printf("DFS (Matrix representation): ");
    for (int i = 0; i < matrixGraph.numVertices; i++) {
        visited[i] = false;
    }
    dfsMatrix(&matrixGraph, 0);
    printf("\n");
    
    printf("DFS (List representation): ");
    for (int i = 0; i < listGraph.numVertices; i++) {
        visited[i] = false;
    }
    dfsList(&listGraph, 0);
    printf("\n\n");
}

void demonstrateBFS() {
    printf("=== BREADTH-FIRST SEARCH ===\n");
    
    GraphMatrix matrixGraph;
    GraphList listGraph;
    createSampleGraph(&matrixGraph, &listGraph);
    
    printf("BFS (Matrix representation): ");
    bfsMatrix(&matrixGraph, 0);
    printf("\n");
    
    printf("BFS (List representation): ");
    bfsList(&listGraph, 0);
    printf("\n\n");
}

void demonstrateDijkstra() {
    printf("=== DIJKSTRA'S ALGORITHM ===\n");
    
    GraphMatrix graph;
    initGraphMatrix(&graph, 5);
    
    // Create a weighted graph
    addEdgeMatrix(&graph, 0, 1, 10, false);
    addEdgeMatrix(&graph, 0, 2, 5, false);
    addEdgeMatrix(&graph, 1, 2, 2, false);
    addEdgeMatrix(&graph, 1, 3, 1, false);
    addEdgeMatrix(&graph, 2, 3, 9, false);
    addEdgeMatrix(&graph, 2, 4, 3, false);
    addEdgeMatrix(&graph, 3, 4, 4, false);
    
    int startVertex = 0;
    dijkstra(&graph, startVertex);
    printDijkstraResults(startVertex, graph.numVertices);
    printf("\n");
}

void demonstrateBellmanFord() {
    printf("=== BELLMAN-FORD ALGORITHM ===\n");
    
    GraphMatrix graph;
    initGraphMatrix(&graph, 5);
    
    // Create a graph with potential negative edges
    addEdgeMatrix(&graph, 0, 1, 6, false);
    addEdgeMatrix(&graph, 0, 2, 7, false);
    addEdgeMatrix(&graph, 1, 2, 8, false);
    addEdgeMatrix(&graph, 1, 3, 5, false);
    addEdgeMatrix(&graph, 1, 4, -4, false);
    addEdgeMatrix(&graph, 2, 3, -3, false);
    addEdgeMatrix(&graph, 2, 4, 9, false);
    addEdgeMatrix(&graph, 3, 1, -2, false);
    addEdgeMatrix(&graph, 4, 3, 7, false);
    
    int startVertex = 0;
    bool noNegativeCycle = bellmanFord(&graph, startVertex);
    
    if (noNegativeCycle) {
        printf("Bellman-Ford results (no negative cycles):\n");
        printDijkstraResults(startVertex, graph.numVertices);
    } else {
        printf("Negative weight cycle detected!\n");
    }
    printf("\n");
}

void demonstrateFloydWarshall() {
    printf("=== FLOYD-WARSHALL ALGORITHM ===\n");
    
    GraphMatrix graph;
    initGraphMatrix(&graph, 4);
    
    // Create a small graph
    addEdgeMatrix(&graph, 0, 1, 5, false);
    addEdgeMatrix(&graph, 0, 3, 10, false);
    addEdgeMatrix(&graph, 1, 2, 3, false);
    addEdgeMatrix(&graph, 2, 3, 1, false);
    
    floydWarshall(&graph);
    printFloydWarshallResults(graph.numVertices);
    printf("\n");
}

void demonstratePrimMST() {
    printf("=== PRIM'S MINIMUM SPANNING TREE ===\n");
    
    GraphMatrix graph;
    initGraphMatrix(&graph, 5);
    
    // Create a weighted graph
    addEdgeMatrix(&graph, 0, 1, 2, false);
    addEdgeMatrix(&graph, 0, 3, 6, false);
    addEdgeMatrix(&graph, 1, 2, 3, false);
    addEdgeMatrix(&graph, 1, 3, 8, false);
    addEdgeMatrix(&graph, 1, 4, 5, false);
    addEdgeMatrix(&graph, 2, 4, 7, false);
    addEdgeMatrix(&graph, 3, 4, 9, false);
    
    printf("Minimum Spanning Tree edges:\n");
    primMST(&graph);
    printf("\n");
}

void demonstrateTopologicalSort() {
    printf("=== TOPOLOGICAL SORT ===\n");
    
    GraphList graph;
    initGraphList(&graph, 6);
    
    // Create a DAG (Directed Acyclic Graph)
    addEdgeList(&graph, 5, 2, 0, true);
    addEdgeList(&graph, 5, 0, 0, true);
    addEdgeList(&graph, 4, 0, 0, true);
    addEdgeList(&graph, 4, 1, 0, true);
    addEdgeList(&graph, 2, 3, 0, true);
    addEdgeList(&graph, 3, 1, 0, true);
    
    topologicalSort(&graph);
    printf("\n");
}

int main() {
    printf("Graph Algorithms\n");
    printf("================\n\n");
    
    demonstrateDFS();
    demonstrateBFS();
    demonstrateDijkstra();
    demonstrateBellmanFord();
    demonstrateFloydWarshall();
    demonstratePrimMST();
    demonstrateTopologicalSort();
    
    printf("All graph algorithms demonstrated!\n");
    return 0;
}
