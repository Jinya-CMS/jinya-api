package bug

type Bug struct {
	Who          string `json:"who"`
	Url          string `json:"url"`
	Title        string `json:"title"`
	Details      string `json:"details"`
	Reproduce    string `json:"reproduce"`
	Severity     string `json:"severity"`
	JinyaVersion string `json:"jinyaVersion"`
	PhpInfo      string `json:"phpInfo"`
}
