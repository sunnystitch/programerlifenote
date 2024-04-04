import execjs
import requests
import json
import time
from Crypto.Cipher import AES


def fetch(url, header={}):
    head = {
        "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36",
        "Cookie":""
    }
    header.update(head)
    print(header)
    count = 0
    while count <= 5:
        try:
            response = requests.get(url, headers=header, timeout=20)
            if response.status_code == 200:
                return response.content
        except Exception as e:
            print(f"{e}")
            count += 1
            if count == 6:
                return bytes("")


# 加载js代码
def JsCompile():
    jsdemo = """function n(t, e) {
    function r(t, e) {
        var r = "";
        if ("object" == typeof t)
            for (var n = 0; n < t.length; n++)
                r += String.fromCharCode(t[n]);
        t = r || t;
        for (var i, o, a = new Uint8Array(t.length), s = e.length, n = 0; n < t.length; n++)
            o = n % s,
                i = t[n],
                i = i.toString().charCodeAt(0),
                a[n] = i ^ e.charCodeAt(o);
        return a
    }
    function n(t) {
        var e = "";
        if ("object" == typeof t)
            for (var r = 0; r < t.length; r++)
                e += String.fromCharCode(t[r]);
        t = e || t;
        var n = new Uint8Array(t.length);
        for (r = 0; r < t.length; r++)
            n[r] = t[r].toString().charCodeAt(0);
        var i, o, r = 0;
        for (r = 0; r < n.length; r++)
            0 != (i = n[r] % 3) && r + i < n.length && (o = n[r + 1],
                n[r + 1] = n[r + i],
                n[r + i] = o,
                r = r + i + 1);
        return n
    }
    function i(t) {
        var e = "";
        if ("object" == typeof t)
            for (var r = 0; r < t.length; r++)
                e += String.fromCharCode(t[r]);
        t = e || t;
        var n = new Uint8Array(t.length);
        for (r = 0; r < t.length; r++)
            n[r] = t[r].toString().charCodeAt(0);
        var r = 0
            , i = 0
            , o = 0
            , a = 0;
        for (r = 0; r < n.length; r++)
            o = n[r] % 2,
            o && r++,
                a++;
        var s = new Uint8Array(a);
        for (r = 0; r < n.length; r++)
            o = n[r] % 2,
                s[i++] = o ? n[r++] : n[r];
        return s
    }

    function o(t, e) {
        var r = 0
            , n = 0
            , i = 0
            , o = 0
            , a = "";
        if ("object" == typeof t)
            for (var r = 0; r < t.length; r++)
                a += String.fromCharCode(t[r]);
        t = a || t;
        var s = new Uint8Array(t.length);
        for (r = 0; r < t.length; r++)
            s[r] = t[r].toString().charCodeAt(0);
        for (r = 0; r < t.length; r++)
            if (0 != (o = s[r] % 5) && 1 != o && r + o < s.length && (i = s[r + 1],
                n = r + 2,
                s[r + 1] = s[r + o],
                s[o + r] = i,
            (r = r + o + 1) - 2 > n))
                for (; n < r - 2; n++)
                    s[n] = s[n] ^ e.charCodeAt(n % e.length);
        for (r = 0; r < t.length; r++)
            s[r] = s[r] ^ e.charCodeAt(r % e.length);
        return s
    }

    for (var a = {
        data: {
            info: t
        }
    }, s = {
        q: r,
        h: n,
        m: i,
        k: o
    }, l = a.data.info, u = l.substring(l.length - 4).split(""), c = 0; c < u.length; c++)
        u[c] = u[c].toString().charCodeAt(0) % 4;
    u.reverse();
    for (var d = [], c = 0; c < u.length; c++)
        d.push(l.substring(u[c] + 1, u[c] + 2)),
            l = l.substring(0, u[c] + 1) + l.substring(u[c] + 2);
    a.data.encrypt_table = d,
        a.data.key_table = [];
    for (var c in a.data.encrypt_table)
        "q" != a.data.encrypt_table[c] && "k" != a.data.encrypt_table[c] || (a.data.key_table.push(l.substring(l.length - 12)),
            l = l.substring(0, l.length - 12));
    a.data.key_table.reverse(),
        a.data.info = l;
    var f = new Array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1);
    a.data.info = function (t) {
        var e, r, n, i, o, a, s;
        for (a = t.length,
                 o = 0,
                 s = ""; o < a;) {
            do {
                e = f[255 & t.charCodeAt(o++)]
            } while (o < a && -1 == e);
            if (-1 == e)
                break;
            do {
                r = f[255 & t.charCodeAt(o++)]
            } while (o < a && -1 == r);
            if (-1 == r)
                break;
            s += String.fromCharCode(e << 2 | (48 & r) >> 4);
            do {
                if (61 == (n = 255 & t.charCodeAt(o++)))
                    return s;
                n = f[n]
            } while (o < a && -1 == n);
            if (-1 == n)
                break;
            s += String.fromCharCode((15 & r) << 4 | (60 & n) >> 2);
            do {
                if (61 == (i = 255 & t.charCodeAt(o++)))
                    return s;
                i = f[i]
            } while (o < a && -1 == i);
            if (-1 == i)
                break;
            s += String.fromCharCode((3 & n) << 6 | i)
        }
        return s
    }(a.data.info);
    for (var c in a.data.encrypt_table) {
        var h = a.data.encrypt_table[c];
        if ("q" == h || "k" == h) {
            var p = a.data.key_table.pop();
            a.data.info = s[a.data.encrypt_table[c]](a.data.info, p)
        } else
            a.data.info = s[a.data.encrypt_table[c]](a.data.info)
    }
    if (e)
        return a.data.info;
    var g = "";
    for (c = 0; c < a.data.info.length; c++)
        g += String.fromCharCode(a.data.info[c]);
    return g
}
function Uint8ArrayToString(fileData) {
    let dataString = "";
    for (var i = 0; i < fileData.length; i++) {
        dataString += String.fromCharCode(fileData[i]);
    }
    return dataString
}
function ArrayToString(t) {
    let string = n(t, true)
    return Uint8ArrayToString(string)
}
function KeyDecrypt(nnn) {
    return Array.prototype.slice.call(new Uint8Array(n(nnn, 1)))
}
"""
    ctx = execjs.compile(jsdemo)
    return ctx


# 解析加密到文字
def ArrayToString(ctx, content):
    return ctx.call("ArrayToString", content)


# 解析 key
def KeyDecrypt(ctx, content):
    print(ctx.call("KeyDecrypt", content))

    return ctx.call("KeyDecrypt", content)


# 下载
def VideoDown(url, filename, key):
    response = requests.get(url)
    content = AesDecrypt(key, response.content)
    with open(filename, "a+b")as f:
        f.write(content)
    return


# AES 视频解密
def AesDecrypt(key, content):
    decrypt = AES.new(bytes(key), AES.MODE_CBC, b'0000000000000000')
    return decrypt.decrypt(content)


def m3u8(url):
    # url = f"https://coding.imooc.com/lesson/m3u8h5?mid={mid}&cid={cid}&ssl=1&cdn=aliyun1&cdn=aliyun1"
    result = fetch(url).decode("utf-8")
    return jiexi(result)


def jiexi(content):
    data = json.loads(content)
    print(data)
    return data.get("data").get("info")


def courseList(cid):
    url = f"https://coding.imooc.com/class/api_freelook?cid={cid}"
    response = fetch(url).decode("utf-8")
    data = json.loads(response)
    datalist = data.get("data")
    print(datalist)
    courselist = []
    mid = datalist[0].get("media_id")
    now = str(int(time.time() * 1000))
    url = f"https://coding.imooc.com/lesson/ajaxchapterlist?v={now}&mid={mid}"
    print(url)
    headers = {
        'Accept': 'application/json, text/javascript, */*; q=0.01',
        'Host': 'coding.imooc.com',
        'Referer': 'https://coding.imooc.com/lesson/355.html',
        'X-Requested-With': 'XMLHttpRequest'
    }
    response = fetch(url, headers).decode("utf-8")

    data = json.loads(response)
    print(data)
    datalist = data.get("data")

    for i in datalist:
        for j in i.get("media_list"):
            if j.get("media_type") == "1":
                coursedict = {}

                coursedict["name"] = f'{i.get("chapter_seqid")}-{j.get("media_seqid")} {j.get("media_name")}'
                coursedict["mid"] = j.get("media_id")
                courselist.append(coursedict)
    print(courselist)
    return courselist


if __name__ == '__main__':
    # cid 为视频网页ID
    cid = "454"
    # 清晰度默认超清
    qxd = "超清"
    ctx = JsCompile()
    response = courseList(cid)

    for j in response:
        mid = j.get("mid")
        name = j.get("name")

        result = m3u8(f"https://coding.imooc.com/lesson/m3u8h5?mid={mid}&cid={cid}&ssl=1&cdn=aliyun1&cdn=aliyun1")
        print(result)
        text = ArrayToString(ctx, result)
        # print(text)
        tv = {}
        for i in text.split("\n"):
            if "https:" in i:
                print(i)
                if "high.m3u8" in i:
                    tv["超清"] = i
                elif "medium.m3u8" in i:
                    tv["高清"] = i
                else:
                    tv["720P"] = i

        result = m3u8(tv.get(qxd))
        text = ArrayToString(ctx, result)
        keyUrl = ""
        count = 1
        urlDict = {}
        for i in text.split("\n"):
            if "METHOD=AES-128" in i:
                keyUrl = i.replace("#EXT-X-KEY:METHOD=AES-128,URI=", "").replace('"', "")
            elif "https:" in i:
                k = f"{count}.ts"
                urlDict[k] = i
                count += 1
        print(keyUrl)
        print(urlDict)
        result = m3u8(keyUrl)

        key = KeyDecrypt(ctx, result)

        for k in urlDict:
            print(k, j.get("name"))

            VideoDown(urlDict.get(k), f"{j.get('name')}.mp4", key)
