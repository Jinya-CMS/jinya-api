package mail

import (
	"net/smtp"
	"os"

	"github.com/scorredoira/email"
)

type Attachment struct {
	Content  string
	Filename string
}

func SendMail(subject string, body string, attachments ...Attachment) error {
	from := os.Getenv("MAIL_FROM")
	to := os.Getenv("MAIL_TO_SUPPORT")
	username := os.Getenv("MAIL_USERNAME")
	password := os.Getenv("MAIL_PASSWORD")
	host := os.Getenv("MAIL_HOST")
	port := os.Getenv("MAIL_PORT")

	message := email.NewHTMLMessage(subject, body)
	message.To = []string{to}
	message.From.Address = from

	for _, attachment := range attachments {
		message.Attachments[attachment.Filename] = &email.Attachment{
			Filename: attachment.Filename,
			Data:     []byte(attachment.Content),
		}
	}

	message.BodyContentType = "text/html"

	var auth smtp.Auth
	if username != "" && password != "" {
		auth = smtp.PlainAuth("", username, password, host)
	}

	err := email.Send(host+":"+port, auth, message)
	if err != nil {
		return err
	}

	return err
}
