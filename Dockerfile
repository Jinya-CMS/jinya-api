FROM golang:latest
WORKDIR /app
COPY . .

RUN go get github.com/joho/godotenv
RUN go get github.com/julienschmidt/httprouter
RUN go get github.com/scorredoira/email

RUN go build -o /jinya-files
EXPOSE 8090

CMD ["/jinya-files", "--self-hosted"]