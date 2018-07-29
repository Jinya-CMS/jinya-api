package routes

import (
	"encoding/json"
	"io/ioutil"
	"log"
	"net/http"

	"../mail"
	"../types"

	"github.com/julienschmidt/httprouter"
)

func Bug(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
	bug := types.Bug{}
	jsn, err := ioutil.ReadAll(r.Body)

	if err != nil {
		log.Fatal("Error reading the body", err)
	}

	err = json.Unmarshal(jsn, &bug)
	if err != nil {
		log.Fatal("Decoding error: ", err)
	}

	submission, err := mail.Send(bug)

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
