* The `logs/` folder must be writeable.

Run the db migration scripts, form the terminal inside the vm, in the /vagrant/api folder run the next command:
vendor/bin/phinx migrate

Create a new migration with command:
vendor/bin/phinx create MigrationName

Create a new seed (TableNameSeeder is the seed name) with the command:
vendor/bin/phinx seed:create TableNameSeeder

Run migrations based on the command:
php bin/phinx migrate

Run all seedes with:
php bin/phinx seed:run

Run a specific seed with the following command:
php bin/phinx seed:run -s TableNameSeeder

You can find more info about phinx migration and seeding:
http://docs.phinx.org/en/latest/migrations.html
http://docs.phinx.org/en/latest/seeding.html
