# Simple Task Management

# Installation 

### Clone repository
```
git@github.com:z-latko/task-management.git
```

### Install dependencies
```
composer install
```

### Configute .env file
```
DATABASE_URL="..."
```

# Run dummy data 
```
php bin/console doctrine:fixtures:load --no-interaction
```

> [!NOTE]  
> Fixtures create default test user with api key **qwerty**

# Example Test API Request

### List projects
```
curl --location 'http://localhost/api/projects' \
  --header 'Content-Type: application/json' \
  --header 'X-Api-Key: qwerty'
```

### Create project 
```
curl --location 'http://localhost/api/projects' \
  --header 'Content-Type: application/json' \
  --header 'X-Api-Key: qwerty' \
  --data '{title": "Test Project", "description": "TEest Project Description"}'
```
