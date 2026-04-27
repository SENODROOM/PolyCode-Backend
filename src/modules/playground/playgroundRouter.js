// "/api/playground": {
//           post: {
//             summary: "Execute JS or Python in the playground",
//             tags: ["Execution"],
//             requestBody: {
//               required: true,
//               content: {
//                 "application/json": {
//                   schema: {
//                     type: "object",
//                     required: ["language", "code"],
//                     properties: {
//                       language: { type: "string", enum: ["javascript", "python", "js", "py"], example: "javascript" },
//                       code: { type: "string", example: "console.log('Hello World')" },
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
//                         output: { type: "string" },
//                         error: { type: "string" },
//                         exitCode: { type: "integer" },
//                       },
//                     },
//                   },
//                 },
//               },
//               400: { description: "Bad request" },
//             },
//           },
//         },
