.PHONY: up down

up:
	python3 -m http.server 8080

down:
	@lsof -ti tcp:8080 | xargs kill 2>/dev/null || echo "Nothing listening on port 8080"
