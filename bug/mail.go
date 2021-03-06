package bug

import (
	"bytes"
	"html/template"

	"../mail"
	"../types"
)

func formatMail(bug Bug) (string, error) {
	mailTpl, err := template.New("bugTicketMail").Parse(`<html>
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
	err = mailTpl.Execute(&tpl, bug)

	if err != nil {
		return "", err
	}

	return tpl.String(), nil
}

func SendMail(bug Bug) (*types.Submission, error) {
	submission := types.Submission{
		FollowUpLink: "",
	}

	ticketTpl, err := formatMail(bug)

	if err != nil {
		return nil, err
	}

	phpInfo := mail.Attachment{
		Content:  bug.PhpInfo,
		Filename: "phpinfo.html",
	}

	err = mail.SendMailTicket(bug.Title, ticketTpl, phpInfo)

	return &submission, err
}
