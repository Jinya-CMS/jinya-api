package like

import (
	"bytes"
	"html/template"

	"../mail"
	"fmt"
	"os"
)

type mailData struct {
	Like           Like
	DevelopersName string
}

func formatMail(like Like) (string, error) {
	mailTpl, err := template.New("likeTicketMail").Parse(`<html>
<body>
<p>Hey {{ .DevelopersName }},</p>
<p>{{ .Like.Who }} likes Jinya CMS 😇.</p>
<p>If you would like to read it, here is a custom message from {{ .Like.Who }}.</p>
{{if .Like.Message}}
<p style="white-space: pre; word-break: keep-all">{{ .Like.Message }}</p>
{{end}}
<p>
    Greetings<br/>
    Jinya Youtrack Bot
</p>
</body>
</html>`)

	if err != nil {
		return "", err
	}

	var tpl bytes.Buffer
	data := mailData{
		Like:           like,
		DevelopersName: os.Getenv("DEVELOPERS_NAME"),
	}
	err = mailTpl.Execute(&tpl, data)

	if err != nil {
		return "", err
	}

	return tpl.String(), nil
}

func SendMail(like Like) error {
	ticketTpl, err := formatMail(like)

	if err != nil {
		return err
	}

	return mail.SendMailToDevelopers(fmt.Sprintf("%s likes Jinya", like.Who), ticketTpl)
}
