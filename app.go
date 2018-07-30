package main

import (
	"net/http"
	"net/http/cgi"
	"os"

	"./bug"
	"./feature"
	"./like"

	_ "github.com/joho/godotenv/autoload"
	"github.com/julienschmidt/httprouter"
)

func main() {
	router := httprouter.New()
	router.POST("/tracker/bug", bug.Route)
	router.POST("/tracker/feature", feature.Route)
	router.POST("/tracker/like", like.Route)

	if len(os.Args) > 1 && os.Args[1] == "self-hosted" {
		err := http.ListenAndServe(":8090", router)
		if err != nil {
			panic(err)
		}
	} else {
		os.Setenv("REQUEST_URI", os.Getenv("REDIRECT_URL"))

		err := cgi.Serve(router)
		if err != nil {
			panic(err)
		}
	}
}
