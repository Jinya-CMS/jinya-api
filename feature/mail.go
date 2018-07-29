package feature

import (
	"bytes"
	"html/template"

	"../mail"
	"../types"
)

func formatMail(feature Feature) (string, error) {
	mailTpl, err := template.New("featureTicketMail").Parse(`<html>
<body>
<h1>{{ .Who }} asked for a new feature in Jinya.</h1>
<p>
    {{ .Details }}
</p>
<p>
    Greetings,<br/>
    Jinya Youtrack Bot
</p>
</body>
</html>`)

	if err != nil {
		return "", err
	}

	var tpl bytes.Buffer
	err = mailTpl.Execute(&tpl, feature)

	if err != nil {
		return "", err
	}

	return tpl.String(), nil
}

func SendMail(feature Feature) (*types.Submission, error) {
	submission := types.Submission{
		FollowUpLink: "",
	}

	ticketTpl, err := formatMail(feature)

	if err != nil {
		return nil, err
	}

	err = mail.SendMail(feature.Title, ticketTpl)

	return &submission, err
}
