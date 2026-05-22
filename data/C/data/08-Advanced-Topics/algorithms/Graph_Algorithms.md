# Graph Algorithms

This file contains implementations of fundamental graph algorithms in C. Graphs are versatile data structures used to model relationships between objects, with applications ranging from social networks to route planning.

## 📚 Graph Fundamentals

### Graph Representations

#### 1. Adjacency Matrix
- **Structure**: 2D array where `matrix[i][j]` represents edge weight
- **Space Complexity**: O(V²) where V is number of vertices
- **Best for**: Dense graphs, quick edge lookup
- **Access Time**: O(1) for edge existence check

#### 2. Adjacency List
- **Structure**: Array of linked lists, one per vertex
- **Space Complexity**: O(V + E) where E is number of edges
- **Best for**: Sparse graphs, memory efficiency
- **Access Time**: O(degree(vertex)) for neighbors

### Graph Types
- **Undirected**: Edges have no direction
- **Directed**: Edges have direction (arcs)
- **Weighted**: Edges have associated weights/costs
- **Unweighted**: All edges have equal weight

## 🔍 Algorithms Implemented

### 1. Depth-First Search (DFS)
**Purpose**: Traverse graph depth-first, visiting as far as possible along each branch
**Applications**: Path finding, cycle detection, topological sorting, maze solving

**Time Complexity**: O(V + E)
**Space Complexity**: O(V) for recursion stack

**Algorithm**:
```c
void dfs(Graph *graph, int vertex) {
    visited[vertex] = true;
    process(vertex);
    
    for each neighbor of vertex:
        if (!visited[neighbor]) {
            dfs(graph, neighbor);
        }
    }
}
```

### 2. Breadth-First Search (BFS)
**Purpose**: Traverse graph breadth-first, visiting all neighbors before moving deeper
**Applications**: Shortest path (unweighted), level-order traversal, connected components

**Time Complexity**: O(V + E)
**Space Complexity**: O(V) for queue

**Algorithm**:
```c
void bfs(Graph *graph, int startVertex) {
    Queue queue;
    enqueue(queue, startVertex);
    visited[startVertex] = true;
    
    while (!isEmpty(queue)) {
        int vertex = dequeue(queue);
        process(vertex);
        
        for each neighbor of vertex:
            if (!visited[neighbor]) {
                visited[neighbor] = true;
                enqueue(queue, neighbor);
            }
        }
    }
}
```

### 3. Dijkstra's Algorithm
**Purpose**: Find shortest paths from source to all vertices (non-negative weights)
**Applications**: GPS navigation, network routing, logistics optimization

**Time Complexity**: O(V²) with array, O(E log V) with priority queue
**Space Complexity**: O(V)

**Algorithm**:
1. Initialize distances: source = 0, others = ∞
2. Visit unvisited vertex with minimum distance
3. Update distances to neighbors
4. Repeat until all vertices visited

### 4. Bellman-Ford Algorithm
**Purpose**: Find shortest paths from source (handles negative weights)
**Applications**: Currency exchange, network routing with negative costs

**Time Complexity**: O(V × E)
**Space Complexity**: O(V)

**Key Features**:
- Handles negative edge weights
- Detects negative weight cycles
- More flexible than Dijkstra but slower

### 5. Floyd-Warshall Algorithm
**Purpose**: Find shortest paths between all pairs of vertices
**Applications**: All-pairs shortest paths, network analysis, transitive closure

**Time Complexity**: O(V³)
**Space Complexity**: O(V²)

**Algorithm**:
```c
for k in 0..V-1:
    for i in 0..V-1:
        for j in 0..V-1:
            if dist[i][k] + dist[k][j] < dist[i][j]:
                dist[i][j] = dist[i][k] + dist[k][j]
```

### 6. Prim's Algorithm (MST)
**Purpose**: Find Minimum Spanning Tree
**Applications**: Network design, circuit design, clustering

**Time Complexity**: O(V²) with array, O(E log V) with priority queue
**Space Complexity**: O(V)

**Algorithm**:
1. Start with arbitrary vertex
2. Add cheapest edge connecting to new vertex
3. Repeat until all vertices included

### 7. Topological Sort
**Purpose**: Linear ordering of vertices in directed acyclic graph (DAG)
**Applications**: Task scheduling, build systems, dependency resolution

**Time Complexity**: O(V + E)
**Space Complexity**: O(V)

**Requirements**: Graph must be a DAG (no cycles)

## 💡 Key Concepts

### Graph Traversal Patterns

#### DFS vs BFS Comparison
| Aspect | DFS | BFS |
|--------|-----|-----|
| Exploration | Deep first | Wide first |
| Data Structure | Stack (recursion) | Queue |
| Memory Usage | Generally less | More for dense graphs |
| Path Finding | Any path | Shortest path (unweighted) |
| Use Cases | Maze solving, cycle detection | Level traversal, shortest path |

### Shortest Path Algorithms Comparison

| Algorithm | Edge Weights | Time Complexity | Negative Cycles |
|-----------|--------------|-----------------|----------------|
| Dijkstra | Non-negative | O(E log V) | No |
| Bellman-Ford | Any | O(V × E) | Detects |
| Floyd-Warshall | Any | O(V³) | Detects |

### Minimum Spanning Tree Algorithms

| Algorithm | Approach | Time Complexity | When to Use |
|-----------|----------|-----------------|-------------|
| Prim | Vertex-focused | O(E log V) | Dense graphs |
| Kruskal | Edge-focused | O(E log E) | Sparse graphs |

## 🚀 Advanced Topics

### 1. Graph Representations Optimization

#### Adjacency Matrix with Compression
```c
// For sparse graphs, use bitsets or compression
typedef struct {
    int *compressed;  // Compressed row storage
    int *indices;    // Row start indices
} CompressedMatrix;
```

#### Adjacency List with Memory Pool
```c
// For high-performance, use memory pools
typedef struct {
    Node *pool;       // Pre-allocated node pool
    int poolIndex;    // Current pool position
} OptimizedGraph;
```

### 2. Specialized Graph Algorithms

#### A* Search
```c
// Heuristic-based pathfinding
int heuristic(int from, int to) {
    // Estimate distance to goal
    return abs(from.x - to.x) + abs(from.y - to.y);
}

void aStar(Graph *graph, int start, int goal) {
    // Priority queue with f = g + h
    // g = actual distance, h = heuristic
}
```

#### Maximum Flow (Ford-Fulkerson)
```c
int fordFulkerson(Graph *graph, int source, int sink) {
    // Find augmenting paths
    // Update residual capacities
    // Return maximum flow
}
```

### 3. Graph Properties and Analysis

#### Connected Components
```c
void findConnectedComponents(Graph *graph) {
    // Use DFS/BFS to find components
    // Mark visited vertices
    // Count and list components
}
```

#### Cycle Detection
```c
bool hasCycle(Graph *graph) {
    // Use DFS with recursion stack
    // Detect back edges
    // Return true if cycle found
}
```

#### Graph Coloring
```c
int graphColoring(Graph *graph, int colors) {
    // Assign colors to vertices
    // No adjacent vertices share color
    // Use backtracking or greedy
}
```

## 📊 Performance Analysis

### Time Complexity Summary

| Algorithm | Best | Average | Worst | Space |
|----------|------|---------|-------|-------|
| DFS | O(V + E) | O(V + E) | O(V + E) | O(V) |
| BFS | O(V + E) | O(V + E) | O(V + E) | O(V) |
| Dijkstra | O(E + V log V) | O(E + V log V) | O(E + V log V) | O(V) |
| Bellman-Ford | O(V × E) | O(V × E) | O(V × E) | O(V) |
| Floyd-Warshall | O(V³) | O(V³) | O(V³) | O(V²) |
| Prim | O(E + V log V) | O(E + V log V) | O(E + V log V) | O(V) |

### Space Optimization Techniques

#### 1. In-place Algorithms
- Modify graph structure directly
- Reduce auxiliary memory usage
- Trade-off: destructive operations

#### 2. Lazy Evaluation
- Compute values on demand
- Cache frequently accessed results
- Trade memory for computation time

#### 3. Bitmask Representations
- Use bits to represent adjacency
- Significant space savings for dense graphs
- Faster bitwise operations

## 🧪 Testing Strategies

### 1. Graph Generation
```c
// Generate random graphs for testing
Graph generateRandomGraph(int vertices, double edgeProbability) {
    Graph graph;
    initGraph(&graph, vertices);
    
    for (int i = 0; i < vertices; i++) {
        for (int j = i + 1; j < vertices; j++) {
            if (random() < edgeProbability) {
                addEdge(&graph, i, j, randomWeight());
            }
        }
    }
    return graph;
}
```

### 2. Special Test Cases
- **Empty Graph**: No vertices or edges
- **Single Vertex**: One vertex, no edges
- **Complete Graph**: All possible edges
- **Linear Graph**: Single path
- **Star Graph**: One central vertex
- **Bipartite Graph**: Two-part vertex sets

### 3. Performance Testing
```c
void benchmarkAlgorithm() {
    clock_t start = clock();
    
    // Run algorithm
    algorithm(&graph);
    
    clock_t end = clock();
    double time = ((double)(end - start)) / CLOCKS_PER_SEC;
    
    printf("Algorithm took %f seconds\n", time);
}
```

## ⚠️ Common Pitfalls

### 1. Memory Management
```c
// Wrong: Forgetting to free memory
Node *newNode = malloc(sizeof(Node));
// Use node...
// Forgot: free(newNode);

// Right: Proper cleanup
Node *newNode = malloc(sizeof(Node));
// Use node...
free(newNode);
```

### 2. Infinite Recursion
```c
// Wrong: No base case or visited check
void dfs(Graph *graph, int vertex) {
    dfs(graph, neighbor); // Can cause stack overflow
}

// Right: Proper base case
void dfs(Graph *graph, int vertex) {
    if (visited[vertex]) return;
    visited[vertex] = true;
    dfs(graph, neighbor);
}
```

### 3. Off-by-One Errors
```c
// Wrong: Incorrect array bounds
for (int i = 0; i <= numVertices; i++) { // Should be <

// Right: Correct bounds
for (int i = 0; i < numVertices; i++) {
```

### 4. Uninitialized Variables
```c
// Wrong: Using uninitialized distances
int distances[MAX_VERTICES];
printf("%d", distances[0]); // Undefined behavior

// Right: Initialize all values
int distances[MAX_VERTICES];
for (int i = 0; i < MAX_VERTICES; i++) {
    distances[i] = INF;
}
```

## 🔧 Real-World Applications

### 1. Social Networks
- **Friend Recommendations**: Graph traversal, similarity measures
- **Influence Propagation**: BFS, centrality algorithms
- **Community Detection**: Clustering algorithms

### 2. Transportation
- **Route Planning**: Dijkstra, A* search
- **Traffic Optimization**: Flow algorithms
- **Public Transit**: Graph connectivity analysis

### 3. Computer Networks
- **Routing Protocols**: Shortest path algorithms
- **Network Topology**: Graph analysis and optimization
- **Load Balancing**: Flow distribution

### 4. Biology
- **Protein Interactions**: Graph-based analysis
- **Evolutionary Trees**: Phylogenetic algorithms
- **Neural Networks**: Graph neural networks

### 5. Games
- **Pathfinding**: A*, Dijkstra
- **Map Generation**: Graph-based procedural generation
- **AI Decision Making**: State space graphs

## 🎓 Learning Path

### Beginner Level
1. **Graph Representations**: Matrix vs List
2. **Basic Traversal**: DFS and BFS
3. **Simple Applications**: Connected components

### Intermediate Level
1. **Shortest Paths**: Dijkstra, Bellman-Ford
2. **Minimum Spanning Trees**: Prim, Kruskal
3. **Topological Sorting**: DAG algorithms

### Advanced Level
1. **Advanced Algorithms**: Floyd-Warshall, A*
2. **Network Flows**: Ford-Fulkerson, max flow
3. **Graph Theory**: Cycles, coloring, matching

## 🔄 Algorithm Selection Guide

### For Shortest Path
- **Unweighted graph**: BFS
- **Non-negative weights**: Dijkstra
- **Negative weights**: Bellman-Ford
- **All pairs**: Floyd-Warshall

### For Traversal
- **Memory efficient**: DFS (recursion)
- **Level by level**: BFS
- **Path reconstruction**: Both with parent tracking

### For Connectivity
- **Connected components**: DFS/BFS
- **Strongly connected**: Kosaraju's algorithm
- **Bipartite testing**: BFS with coloring

Graph algorithms form the foundation of many computer science applications. Master these implementations to solve complex problems involving relationships and networks!
