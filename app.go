package main

import (
	"./bug"
	"./feature"
	"./like"
	"net/http"

	_ "github.com/joho/godotenv/autoload"
	"github.com/julienschmidt/httprouter"
)

func main() {
	router := httprouter.New()
	router.POST("/tracker/bug", bug.Route)
	router.POST("/tracker/feature", feature.Route)
	router.POST("/tracker/like", like.Route)

	err := http.ListenAndServe(":8090", router)
	if err != nil {
		panic(err)
	}
}
