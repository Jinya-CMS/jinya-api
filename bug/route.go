package bug

import (
	"encoding/json"
	"io/ioutil"
	"log"
	"net/http"

	"github.com/julienschmidt/httprouter"
)

func Route(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
	bug := Bug{}
	jsn, err := ioutil.ReadAll(r.Body)

	if err != nil {
		log.Fatal("Error reading the body", err)
	}

	err = json.Unmarshal(jsn, &bug)
	if err != nil {
		log.Fatal("Decoding error: ", err)
	}

	submission, err := SendMail(bug)

	w.Header().Set("Content-MimeType", "application/json")
	if err != nil {
		w.WriteHeader(http.StatusInternalServerError)
		responseBody, _ := json.Marshal(err.Error())
		w.Write(responseBody)
	} else {
		responseBody, _ := json.Marshal(submission)
		w.Write(responseBody)
	}
}
