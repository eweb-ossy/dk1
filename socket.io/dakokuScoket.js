const https = require('https');
const fs = require('fs');
const request = require('request');

const ssloptions = {
    key: fs.readFileSync('/etc/letsencrypt/live/dakoku.work/privkey.pem', 'utf8'),
    cert: fs.readFileSync('/etc/letsencrypt/live/dakoku.work/cert.pem', 'utf8')
};
const port = 3010;
const server = https.createServer(ssloptions, (req, res) => {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Request-Method', '*');
    res.setHeader('Access-Control-Allow-Methods', 'OPTIONS, GET');
    res.setHeader('Access-Control-Allow-Headers', '*');
    if ( req.method === 'OPTIONS' || req.method === 'GET' ) {
        res.writeHead(200);
        res.end();
        return;
    }
}).listen(port);

const io = require('socket.io')(server, {
    cors: {
        origins: ["http://localhost:8000", "*"],
        methods: ["GET", "POST"],
        credentials: true
    }
});

// 出退勤状況
const nowUsers = io.of('/nowusers').on('connection', socket => {
    socket.on('system_id', system_id => {
        socket.join(system_id);
        request.post({
            uri: `https://${system_id}.dk-keeper.com/api/status`,
            headers: {'Content-type': 'application/json'}
        }, (err, res, data) => {
            nowUsers.to(system_id).emit('nowusers_server_to_client', JSON.parse(data));
        });
    });
});

// 申請データ
const noticeData = io.of('/notice/data').on('connection', socket => {
    socket.on('notice_client_to_server', notice => {
        socket.join(notice.system_id);
        request.get({
            uri: `https://${notice.system_id}.dk-keeper.com/api/notice/get_data?user_id=${notice.user_id}`,
            headers: {'Content-type': 'application/json'}
        }, (err, res, data) => {
            noticeData.to(notice.system_id).emit('notice_server_to_client', JSON.parse(data));
        });
    });
});

// 申請内容
const noticeText = io.of('/notice/text').on('connection', socket => {
    socket.on('notice_text_client_to_server', notice => {
        socket.join(notice.notice_id);
        request.get({
            uri: `https://${notice.system_id}.dk-keeper.com/api/notice/get_id_data?notice_id=${notice.notice_id}`,
            headers: {'Content-type': 'application/json'}
        }, (err, res, data) => {
            noticeText.to(notice.notice_id).emit('notice_text_server_to_client', JSON.parse(data));
        });
    });
});