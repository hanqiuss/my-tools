const { execSync, spawn,spawnSync } = require('child_process');
const fs = require('fs')
const http = require("http");

const compress_name = 'static.zip';

const bt_url=''
const bt_token_url = bt_url + '/2ed56f0e/'
const bt_username = ''
const bt_password = ''
const upload_dir = '/www/wwwroot/hishopmall.cn/LaiKeAdmin/H5'

const store_file = 'cookie.json'

const agent = new http.Agent({keepAlive:true});

try{
  fs.accessSync(compress_name,fs.constants.F_OK)
  fs.unlinkSync(compress_name)
}catch (e){}

const zip = spawnSync('7z',['-tzip', 'a', 'static.zip', './dist/build/h5/*'])

if(zip.status || zip.signal){
  console.log('compress error')
  console.error(zip.stderr.toString())
  return 1
}



run().catch(e=>console.log(e))

async function run(){
  let post_data = `username=${bt_username}&password=${bt_password}&code=`
  const header = {
    Referer:bt_token_url
  }
  try {
    let store_data = '';
    let cookie = '';
    let req_token = '';
    let x_token = '';
    try {
      fs.accessSync(store_file, fs.constants.F_OK);
      store_data = fs.readFileSync(store_file)
      store_data && (store_data = JSON.parse(store_data))
      if( store_data && ((new Date()).getTime()-store_data.time < 86400000) ){
        cookie = store_data.cookie;
        req_token = store_data.req_token;
        if(store_data.x_token){
          x_token = store_data.x_token
        }
      }
    } catch (err) {
      console.error('no cookie file!');
    }
    if(cookie === ''){
      let ret = await post(bt_url + '/login',post_data,header);
      let data = JSON.parse(ret.data)
      if(!data.status){
        console.log('login fail ', data.msg)
        return;
      }
      cookie = ret.header['set-cookie'].map(x=>x.split(';')[0]).join('; ')
      cookie.split('; ').map(x=>x.split('=')).map(x=>{
        x[0]=== 'request_token' && (req_token = x[1])
      })
      store_data = {
        cookie:cookie,
        req_token:req_token,
        time:(new Date()).getTime(),
      }
      fs.writeFileSync(store_file,JSON.stringify(store_data))
      console.log('login bt');
    }

    header['Referer'] = bt_token_url
    header['Cookie'] = cookie
    if(x_token === ''){
      let ret2 = await get(bt_url+'/', {headers:header,agent:agent})
      const regex = /id="request_token_head" token="([\w\d]+?)"\>/
      const found = ret2.data.match(regex);
      if(!found){
        console.log('get token error', ret2)
        return;
      }
      x_token = found[1]
      store_data['x_token'] = x_token
      store_data['time'] = (new Date()).getTime();
      fs.writeFileSync(store_file,JSON.stringify(store_data))
    }

    header['Referer'] = bt_url + '/files'
    header['x-http-token'] = x_token;
    header['x-cookie-token'] = req_token;

    let ret3 = await upload(compress_name,upload_dir,header);
    let r3 = JSON.parse(ret3.data)
    if(!r3.status){
      console.log('upload failed : ' , ret3)
      return;
    }
    console.log(r3.msg)
    const sfile = upload_dir + '/static.zip'
    post_data = `sfile=${encodeURIComponent(sfile)}&dfile=${encodeURIComponent(upload_dir)}&type=zip&coding=UTF-8&password=`

    let ret4 = await post(bt_url + '/files?action=UnZip',post_data,header);
    //console.log(ret4)
    let r4 = JSON.parse(ret4.data)
    if(!r4.status){
      console.log('unzip failed : ' , ret4)
      return;
    }
    console.log(r4.msg)
  } catch (e) {
    console.log('http error : ',e);
    return
  }
}



function upload(file,path,header){
  let file_content = fs.readFileSync(file);
  let size = Buffer.byteLength(file_content);
  let data = `-----------------------------4928236463327821194247933281\r\n`
    +`Content-Disposition: form-data; name="f_path"\r\n\r\n`
    +`${path}\r\n`
    +`-----------------------------4928236463327821194247933281\r\n`
    +`Content-Disposition: form-data; name="f_name"\r\n\r\n`
    +`${file}\r\n`
    +`-----------------------------4928236463327821194247933281\r\n`
    +`Content-Disposition: form-data; name="f_size"\r\n\r\n`
    +`${size}\r\n`
    +`-----------------------------4928236463327821194247933281\r\n`
    +`Content-Disposition: form-data; name="f_start"\r\n\r\n`
    +`0\r\n`
    +`-----------------------------4928236463327821194247933281\r\n`
    +`Content-Disposition: form-data; name="blob"; filename="blob"\r\n`
    +`Content-Type: application/octet-stream\r\n\r\n`;

  data = Buffer.from(data)
  data = Buffer.concat([data,file_content,Buffer.from('\r\n-----------------------------4928236463327821194247933281--\r\n')])

  let _header = {
    ...header,
    'Content-Type': 'multipart/form-data; boundary=---------------------------4928236463327821194247933281',
    Referer: bt_url + '/files',
    Cookie: header['Cookie'],
  }

  return post(bt_url + '/files?action=upload', data,_header)
}


function post(url,data,header){
  return new Promise(function (resolve, reject){

    const options = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'Content-Length': Buffer.byteLength(data),
        ...header
      },
      agent:agent
    };
    const req = http.request(url,options, (res) => {
      res.setEncoding('utf8');
      let rawData = '';
      res.on('data', (chunk) => {
        rawData += chunk;
      });
      res.on('end', () => {
        //console.log(res)
        resolve({data:rawData,header:res.headers})
      });
    });
    req.on('error', (e) => {
      reject(e)
    });
    req.write(data);
    req.end();
  })
}
function get(url,option){

  return new Promise(function (resolve, reject){
    http.get(url,option,res=>{
      //res.setEncoding('utf8');
      let rawData = '';
      res.on('data', (chunk) => { rawData += chunk; });
      res.on('end', () => {
        //console.log(res)
        resolve({data:rawData,header:res.headers})
      });
    }).on('error',e=>reject(e))
  })
}
