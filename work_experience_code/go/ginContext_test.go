package service

import (
	"bytes"
	"encoding/json"
	"github.com/gin-gonic/gin"
	"io/ioutil"
	"net/http"
	"net/http/httptest"
)

type myResp map[string]interface{}

func (r myResp) total() int {
	if data, ok := r["data"].(map[string]interface{}); ok {
		if f, ok := data["totalCount"].(float64); ok {
			return int(f)
		}
	}
	return -1
}

func (r myResp) pageLen() int {
	if page, ok := r["page"].(map[string]interface{}); ok {
		if arr, ok := page["result"].([]interface{}); ok {
			return len(arr)
		}
	}
	return -1
}

func (r myResp) getStr(key string) string {
	if data, ok := r["data"].(map[string]interface{}); ok {
		if val, ok := data[key].(string); ok {
			return val
		}
	}
	return ""
}

func (r myResp) getInt(key string) int {
	if val, ok := r[key].(float64); ok {
		return int(val)
	}
	return 0
}

type myWriter struct {
	gin.ResponseWriter
	body *bytes.Buffer
}

func (w myWriter) Write(b []byte) (int, error) {
	w.body.Write(b)
	// fmt.Printf("write %d bytes\n", len(b))
	return w.ResponseWriter.Write(b)
}

func (w myWriter) toMyResp() myResp {
	resp := map[string]interface{}{}
	json.Unmarshal(w.body.Bytes(), &resp)
	return resp
}

// args: 0 - current user
func newTestContext(
	params map[string]string,
	query map[string]string,
	body interface{},
	args ...interface{}) (*gin.Context, myWriter) {

	c, _ := gin.CreateTestContext(httptest.NewRecorder())
	// we dont use method and url inside our handlers
	c.Request, _ = http.NewRequest("", "", nil)
	c.Params = []gin.Param{}
	for k, v := range params {
		c.Params = append(c.Params, gin.Param{k, v})
	}
	q := c.Request.URL.Query()
	for k, v := range query {
		q.Add(k, v)
	}
	c.Request.URL.RawQuery = q.Encode()
	var j []byte
	if body != nil {
		j, _ = json.Marshal(body)
		c.Request.Body = ioutil.NopCloser(bytes.NewReader(j))
		c.Request.ContentLength = int64(len(j))
	}

	// xxx: unit tests that use `currentUser` is supposed to set explicitly in test routine
	c.Set("currentUser", &database.UserModel{UserID: conf.AdminUserID, Name: "__default_unit_test_user__", Role: "admin"})
	if len(args) > 0 {
		if user, ok := args[0].(database.UserModel); ok {
			c.Set("currentUser", &user)
		}
	}
	c.Set("requestID", "__default_unit_test_request_id__")

	// we replace default response writer with our own writer so that we can read response latter
	c.Writer = myWriter{body: bytes.NewBufferString(""), ResponseWriter: c.Writer}
	return c, c.Writer.(myWriter)
}

// demo
//
/*
// demo-1 c, _ := newTestContext(nil, nil, request)
// demo-2
c, _ := newTestContext(nil, map[string]string{
			"categoryUUID": "test_uuid",
			"sampleUUID":   "test_uuid",
			"points":       "0",
		}, nil)
 */

// gin 自带测试
// ctx, _ := gin.CreateTestContext(httptest.NewRecorder())
