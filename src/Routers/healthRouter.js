// "/api/health": {
//           get: {
//             summary: "Health check",
//             tags: ["System"],
//             responses: {
//               200: {
//                 description: "Server is healthy",
//                 content: {
//                   "application/json": {
//                     schema: {
//                       type: "object",
//                       properties: {
//                         status: { type: "string", example: "OK" },
//                         timestamp: { type: "string", format: "date-time" },
//                         message: { type: "string" },
//                       },
//                     },
//                   },
//                 },
//               },
//             },
//           },
//         },
