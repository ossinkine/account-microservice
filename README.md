User accounts microservice
==========================

The application stores user IDs and their balance.
Interaction with it is carried out using message broker (RabbitMQ).
The application can perform one of the following operations with the user account:
- Debit
- Credit
- Authorization (Blocking of funds. Blocked funds are not available for use. Blocking means that some operation is on authorization and waiting for some external confirmation, it can be subsequently confirmed or rejected)
- Capture blocked funds
- Void blocked funds
- Transfer between users 

An event is generated after any of these operations.

Installation
============

    git clone git@github.com:ossinkine/account-microservice.git
    cd account-microservice
    composer install
    docker-compose up --scale worker=3 --detach # or any other workers number
    docker-compose exec worker bin/console doctrine:migrations:migrate --no-interaction

Test experience
===============

View created accounts and their balance:
    
    docker-compose exec worker bin/console app:dump

Creating a task to perform an operation on the account:

    docker-compose exec worker bin/console app:transaction <user_id> <type> <amount>
    
`user_id` - any string, user ID  
`type` - one of the following: debit, credit, auth, void, capture  
`amount` - amount with two decimal places (123.45)  
The option `--quantity=100` can be specified for creating several tasks to test the parallel work of workers  

Creating a task for transfer between accounts:
    
    docker-compose exec worker bin/console app:transfer <source_user_id> <destination_user_id> <amount>

`source_user_id` - any string, user ID from whose account the money is debited  
`destination_user_id` - user ID in favor of which transfer  
`amount` - _no comments_  
The option `--quantity=100` is also available  
The option `--reverse` can be specified for additional creating of reverse transfer to test deadlocks

View worker logs:
    
    docker-compose logs worker

Known issues
============
- Sometimes the worker can not connect to the queue server, after an error it reconnects and works correctly.
- Memory leaks, but this is not critical. The `--memory-limit` flag allows the daemon restarts when a certain memory consumption is reached.
