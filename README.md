@ToDo:
1. Because of server problem could not use curl for api calls and had to substitute (to fix server and replace with curl calls)
2. Find a server on cloud and put code there
3. For the 'frontend' application - replace calls to db for authentication with api cals to 'backend'
4. More unitTests for backend

Some screen shots with the admin can be seen here: https://github.com/marcubn/symfony-api-back/wiki


Two symfony (3.4) applications. One for 'backend' and one for 'frontend'
For the momment installed locally

Backend (running on http://127.0.0.1:8000 - php bin/console server:start):
- receives api calls for:
    - getting all offers from DB - GET - api/V1/offer
    - getting one offer based on id - GET - api/V1/offer/{$id}
    - adding a new offer - POST - api/V1/offer
    - updating a new offer - PUT - api/V1/offer
    - deleting an offer - DELETE - api/V1/offer
    
Frontend (running on http://127.0.0.1:8080 - php bin/console server:start http://127.0.0.1:8080):
- sends api calls to backend to get information about:
    - all offers - offer/
    - one offer - offer/{$id}
    - edit offer - offer/{$id}/edit
    - delete offer - offer/delete/{$id}
    - adding offer - offer/new
    
- only available to authenticated users (user checks directly with db - to be moved on api calls to backend on the future):
    - register new user - /register
    - login new user - /login
    - logout user - /logout
    
    
