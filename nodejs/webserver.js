/**
 * Created by edohyune on 2016-03-22.
 */

const http = require('http');

const hostname = '61.102.162.114';
const port = 1337;

http.createServer((req, res) => {
    res.writeHead(200, { 'Content-Type': 'text/plain' });
res.end('Hello World\n');
}).listen(port,hostname () => {
    console.log(`Server running at http://${hostname}:${port}/`);
});