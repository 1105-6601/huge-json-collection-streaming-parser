## Run docker

```
docker-compose up -d
```

## Test

```
docker exec -it {container-id} bash
cd /app
vendor/bin/phpunit
```

## Package publish

- Commit and Push. Then

```
git tag {version}
git push origin --tags
```
