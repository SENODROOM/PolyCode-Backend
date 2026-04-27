// "/api/documents": {
//           get: {
//             summary: "List documents",
//             tags: ["Documents"],
//             parameters: [
//               { name: "language", in: "query", schema: { type: "string" }, description: "Language folder (e.g. Python)" },
//               { name: "category", in: "query", schema: { type: "string" } },
//               { name: "fileType", in: "query", schema: { type: "string", enum: ["py", "md", "js", "txt"] } },
//               { name: "search", in: "query", schema: { type: "string" } },
//               { name: "page", in: "query", schema: { type: "integer", default: 1 } },
//               { name: "limit", in: "query", schema: { type: "integer", default: 20 } },
//               { name: "ungrouped", in: "query", schema: { type: "string", enum: ["true", "false"] } },
//             ],
//             responses: {
//               200: {
//                 description: "Paginated list of documents",
//                 content: {
//                   "application/json": {
//                     schema: {
//                       type: "object",
//                       properties: {
//                         documents: { type: "array", items: { $ref: "#/components/schemas/Document" } },
//                         total: { type: "integer" },
//                         page: { type: "integer" },
//                         pages: { type: "integer" },
//                       },
//                     },
//                   },
//                 },
//               },
//             },
//           },
//         },
//         "/api/documents/stats": {
//           get: {
//             summary: "Document statistics",
//             tags: ["Documents"],
//             parameters: [{ name: "language", in: "query", schema: { type: "string" } }],
//             responses: {
//               200: {
//                 description: "Stats",
//                 content: {
//                   "application/json": {
//                     schema: {
//                       type: "object",
//                       properties: {
//                         totalDocuments: { type: "integer" },
//                         totalWords: { type: "integer" },
//                         byCategory: { type: "array", items: { type: "object" } },
//                         byFileType: { type: "array", items: { type: "object" } },
//                       },
//                     },
//                   },
//                 },
//               },
//             },
//           },
//         },
//         "/api/documents/categories": {
//           get: {
//             summary: "List categories",
//             tags: ["Documents"],
//             parameters: [{ name: "language", in: "query", schema: { type: "string" } }],
//             responses: {
//               200: {
//                 description: "Category names",
//                 content: { "application/json": { schema: { type: "array", items: { type: "string" } } } },
//               },
//             },
//           },
//         },
//         "/api/documents/languages": {
//           get: {
//             summary: "List available language folders",
//             tags: ["Documents"],
//             responses: {
//               200: {
//                 description: "Languages",
//                 content: {
//                   "application/json": {
//                     schema: {
//                       type: "object",
//                       properties: { languages: { type: "array", items: { type: "string" } } },
//                     },
//                   },
//                 },
//               },
//             },
//           },
//         },
//         "/api/documents/tree": {
//           get: {
//             summary: "Get folder/file tree",
//             tags: ["Documents"],
//             parameters: [{ name: "language", in: "query", schema: { type: "string" } }],
//             responses: {
//               200: {
//                 description: "Nested tree",
//                 content: {
//                   "application/json": {
//                     schema: {
//                       type: "object",
//                       properties: { tree: { type: "array", items: { type: "object" } } },
//                     },
//                   },
//                 },
//               },
//             },
//           },
//         },
//         "/api/documents/run-python": {
//           post: {
//             summary: "Execute Python code",
//             tags: ["Execution"],
//             requestBody: {
//               required: true,
//               content: {
//                 "application/json": {
//                   schema: {
//                     type: "object",
//                     required: ["code"],
//                     properties: {
//                       code: { type: "string", example: "print('Hello World')" },
//                       stdin: { type: "string", example: "" },
//                     },
//                   },
//                 },
//               },
//             },
//             responses: {
//               200: {
//                 description: "Execution result",
//                 content: {
//                   "application/json": {
//                     schema: {
//                       type: "object",
//                       properties: {
//                         stdout: { type: "string" },
//                         stderr: { type: "string" },
//                         error: { type: "string", nullable: true },
//                         exitCode: { type: "integer" },
//                       },
//                     },
//                   },
//                 },
//               },
//             },
//           },
//         },
//         "/api/documents/{filePath}": {
//           get: {
//             summary: "Get a specific file",
//             tags: ["Documents"],
//             parameters: [
//               {
//                 name: "filePath",
//                 in: "path",
//                 required: true,
//                 description: "e.g. Python/data/sorting/bubble_sort.py",
//                 schema: { type: "string" },
//               },
//               { name: "language", in: "query", schema: { type: "string" } },
//             ],
//             responses: {
//               200: { description: "File content", content: { "application/json": { schema: { $ref: "#/components/schemas/Document" } } } },
//               403: { description: "Access denied" },
//               404: { description: "Not found" },
//             },
//           },
//         },
