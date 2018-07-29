package mail

import (
	"bytes"
	"html/template"

	"../types"
)

func formatFeature(feature types.Feature) (string, error) {
	mail, err := template.New("featureTicketMail").Parse(`<html>
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
	err = mail.Execute(&tpl, feature)

	if err != nil {
		return "", err
	}

	return tpl.String(), nil
}

func SendFeature(feature types.Feature) (*types.Submission, error) {
	submission := types.Submission{
		FollowUpLink: "",
	}

	ticketTpl, err := formatFeature(feature)

	if err != nil {
		return nil, err
	}

	err = SendMail(feature.Title, ticketTpl)

	return &submission, err
}
