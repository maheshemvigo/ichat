Generate or update PHPUnit tests.

## Agent

`.cursor/agents/test-writer.md`

## Steps

1. Read target file and existing tests in `tests/`
2. Cover happy path, errors, auth (admin vs participant), chat edge cases
3. Mock Pusher, S3, OpenAI — no real external calls
4. Run: `docker compose exec app vendor/bin/phpunit`

## Target

$ARGUMENTS
