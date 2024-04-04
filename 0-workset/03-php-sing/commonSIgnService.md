
## 统一签名说明
### 一.签名传参（统一在header头里面进行传递）

sign|签名值
|--|--|
ak|调用方标识，服务端根据此标识匹配SK秘钥 
timestamp |签名时间戳，服务端需要进行过期检测
ak |调用方标识，服务端根据此标识匹配SK秘钥


### 二.签名步骤
#### 1.GET请求
- 将URL请求中的传参构造成url方式得到urlstring如：a=1&b=2&c=3
- 根据请求来源确定加密秘钥SK（秘钥可以是设备ID+动态token根据业务进行确定）
- 拼接加密串 urlstring+timestamp(请求参数+请求时间)如：a=1&b=2&c=31565233578
- 使用hmac方式的sha256将串加密，然后再base64的到签名串
    php代码示例：
    base64_encode(hash_hmac('sha256', 'a=1&b=2&c=31565233578', 'SK', true))
- 将签名sign和签名时间timestamp=1565233578放入header头传入


#### 2.POST请求-formurlencode格式
- 统一设置header请求格式Content-Type:application/x-www-form-urlencoded 
- 请求参数构造成url方式并得到(按参数名字母排序)urlstring如：a=b&c=d%22%5Ctest   abc=1&abd=2&abe=3&bab=4 
- 根据请求来源确定加密秘钥SK
- 拼接加密串 urlstring+timestamp(请求参数+请求时间)如：a=b&c=d%22%5Ctest1565233578
- 使用hmac方式的sha256将串加密，然后再base64的到签名串
  php代码示例：
  base64_encode(hash_hmac('sha256', 'a=b&c=d%22%5Ctest1565233578', 'SK', true))
- 将签名sign和timestamp放入header头传入


#### 3.POST请求form-data格式（兼容模式-不建议使用）
- 统一设置header请求格式Content-Type:multipart/form-data
- 同二


#### 4.POST请求JSON格式
- 统一设置header请求格式Content-Type:application/json
- 需要传入的json串jsonstring如：{"a":"b","c":123}
- 根据请求来源确定加密秘钥SK
- 拼接加密串 jsonstring+timestamp(请求参数+请求时间)如：{"a":"b","c":123}1565233578
- 使用hmac方式的sha256将串加密，然后再base64的到签名串
    php代码示例：
    base64_encode(hash_hmac('sha256', '{"a":"b","c":123}1565233578', 'SK', true))
- 将签名sign和timestamp放入header头传入


### 三.签名示例
1. GET方式
2. POST x-www-form-urlencoded方式
3. POST JSON方式


### 四.其他代码加密实例
> https://www.jokecamp.com/blog/examples-of-creating-base64-hashes-using-hmac-sha256-in-different-languages/


### 五：相关代码封装SDK参考
PHP：CommonSignService.php