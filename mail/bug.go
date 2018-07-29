package mail

import (
	"bytes"
	"html/template"

	"../types"
)

func formatBug(bug types.Bug) (string, error) {
	mail, err := template.New("bugTicketMail").Parse(`<html>
<body>
<h1>{{ .Who }} found a bug in Jinya {{ .JinyaVersion }}.</h1>
<p>
    They say its severity is <i>{{ .Severity }}</i>.<br/>
    The bug is available under this url {{ .Url }}
</p>
<h2>The details</h2>
<p>
    {{ .Details }}
</p>
<h2>Reproduce it</h2>
<p>
    {{ .Reproduce }}
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
	err = mail.Execute(&tpl, bug)

	if err != nil {
		return "", err
	}

	return tpl.String(), nil
}

func SendBug(bug types.Bug) (*types.Submission, error) {
	submission := types.Submission{
		FollowUpLink: "",
	}

	ticketTpl, err := formatBug(bug)

	if err != nil {
		return nil, err
	}

	phpInfo := Attachment{
		Content:  bug.PhpInfo,
		Filename: "phpinfo.html",
	}

	err = SendMail(bug.Title, ticketTpl, phpInfo)

	return &submission, err
}
