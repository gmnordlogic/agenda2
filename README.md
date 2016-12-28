# AngularUI Agenda 2
Demonstrates how is build an AngularJS application using best practices, based on AngularUI and Bootstrap, and of course, PHP and MySQL for the api.

## Version
2.0.0

## Release Date
02/03/2016

## What the application do?
With this app you can record your contacts. After that, you can edit them or remove them from your application. All information are saved into MySQL database.

## Structure
	/presentation
		/assets
			/css
			/images
			/js
		/client
			/app
			/content
	/rest
	
## Requirements
- Install Vagrant

## Running
Runs locally, database is required.

## Setup instructions

### Setup Vagrant for local development
For Windows/OS-X/Linux you must follow the next steps:
 - Download and install [Virtualbox]
 - Download and install [Vagrant]
 - Go to folder where the application is
 - Modify hosts file adding the line:
```192.168.33.170 agenda.dev```
. The file can be found on ```/etc/hosts``` on OS-X/Linux or ```%SystemRoot%\System32\drivers\etc\hosts``` on Windows
 - Open a terminal (on OS-X or Linux) or command prompt (on Windows)
 - run command: ```vagrant up```
 - to access the shell of the virtual machine type on a terminal (you must be in the application folder): ```vagrant ssh```
 - Now you can access the application on your prefered browser: ```http://agenda.dev/ ```
 
### How to access the API
If you need to access the API, go: ```http://agenda.dev/api/ ``` 
You can use some parameters:
 - ```all``` - to get all contacts from agenda - with GET method
 - ```count``` - to get count for how much contacts have your agenda - with GET method
 - ```contact``` - to save a contact - with POST method (you must send the contact in json format)
 - ```contact/:id``` - to update a contact, where :id is the contact id - with PUT method (you must send the contact in json format)
 - ```contact/:id``` - to delete a contact, where :id is the contact id - with DELETE method

### Running on a server or local environment like XAMPP or WAMP or MAMP
 - Download the application
 - copy to your server or locally and unzip it in a folder named (e.g.) ```agenda```
 - access the application from your browser like: ```http://your_server/agenda/```

## The end
That's all. For any questions please contact me at: ```gheorghe@morodan.com``` 

**Bye**

[//]: #
   [Virtualbox]: <http://virtualbox.org>
   [Vagrant]: <http://vagrantup.com>
