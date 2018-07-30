package like

import (
	"encoding/json"
	"github.com/julienschmidt/httprouter"
	"io/ioutil"
	"log"
	"net/http"
)

func Route(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
	feature := Like{}
	jsn, err := ioutil.ReadAll(r.Body)

	if err != nil {
		log.Fatal("Error reading the body", err)
	}

	err = json.Unmarshal(jsn, &feature)
	if err != nil {
		log.Fatal("Decoding error: ", err)
	}

	err = SendMail(feature)

	w.Header().Set("Content-Type", "application/json")
	if err != nil {
		w.WriteHeader(http.StatusInternalServerError)
		responseBody, _ := json.Marshal(err.Error())
		w.Write(responseBody)
	} else {
		w.WriteHeader(http.StatusNoContent)
	}
}
