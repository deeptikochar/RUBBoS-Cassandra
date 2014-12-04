RUBBoS-Cassandra
================

##Cassandra implementation of RUBBoS

##Installation

###Install Cassandra

1.	Gain root access:    
sudo su   
2.	Check if Java is installed:   
java -version   
If not, install the latest JDK :   
apt-get install open-jdk   
3.	Add DataStax to the repository:   
vi /etc/apt/sources.list   
Insert at the end: deb http://debian.datastax.com/community stable main   
4.	Add the Datastax repo key:   
curl -L http://debian.datastax.com/debian/repo_key | sudo apt-key add -   
apt-get update   
5.	Install the Python driver:   
apt-get install python-cql  
6.	Install Cassandra:   
apt-get install Cassandra   
7.	Remove the test DB:   
service cassandra stop   
bash -c 'rm /var/lib/cassandra/data/system/*'   
8.	Start Cassandra   
service Cassandra start


###Load the database

1.	Download the database from RUBBoS (http://forge.ow2.org/project/showfiles.php?group_id=129)   
2.	Untar it into the RUBBoS/database directory   
3.	Go to the RUBBoS/database directory   
4.	Run the python script portdata.py to generate new csv files suitable for Cassandra   
python portdata.py   
5.	Start the Cassandra shell   
cqlsh   
6.	Run the cql scripts to create the database and load data   
source ‘rubbos.cql’   
source ‘load.cql’   
source ‘test.cql’   
7.	A new keyspace ‘rubbos’ has been created and loaded with data. You can now close the shell by typing ‘exit’.   



Install Apache   
 
1.	Run the following command:   
sudo apt-get install apache2   
2.	To check if apache is correctly installed, go to 127.x.x.x from the browser and it should display the apache start page.   

Key points :   

1. Apache’s webstore is by default at :    
/var/www/html/   
2. Apache’s conf file is located at:   
/etc/apache2/apache2.conf   
3.The complete configuration documentation is at:  
/usr/share/doc/apache2/README.Debian.gz   



Install PHP5   

1.	Run the following command    
apt-get install php5 libapache2-mod-php5   
2.	Restart Apache    
sudo service apache2 restart   
3.	Install an opcode cacher to boost PHP performance:    
apt-get install php5-xcache    
service apache2 restart   
4.	To check if PHP is working with apache, create a file in the apache webstore: 
vi /var/www/html/info.php   
With the following contents:   

<?php    
   phpinfo();    
?>   

Now, if you go to 127.0.0.1/info.php, it should give you a page with info about your php version.   


Set up RUBBoS with php-cassandra-binary and UUID generator:    


1.	Create a directory for your project in /var/www/html and go to it:   
mkdir /var/www/html/rubbos    
cd /var/www/html/rubbos    
2.	Install composer.json for this location:   
sudo su    
curl -sS https://getcomposer.org/installer | php    
3.	Create a composer.json file:    
vi composer.json    
4.	To check if composer is working:    
php composer.phar    
5.	Add the content to composer.json:    
{    
"require": {    
       "evseevnn/php-cassandra-binary": "dev-master",    
       "duoshuo/uuid": "dev-master"    
   }   
}    
6.	Install the composer content into the project:    
php composer.phar install    
7.	Copy the RUBBoS PHP files to /var/www/html/rubbos    
cp –r <path to RUBBoS directory> /RUBBoS/PHP /var/www/html/rubbos    


###Run the benchmark

1.	Install sysstat, open-jdk and gnuplot on all nodes    
sudo apt-get install sysstat   
sudo apt-get install open-jdk    
sudo apt-get install gnuplot    
2.	Go to the Client directory    
cd Client    
3.	Edit the rubbos.properties to put in the correct paths and clients and server addresses.    
4.	Compile the client    
make client    
5.	Go to the RUBBoS directory and compile and run the emulator    
cd ..    
sudo make emulator                  (This command compiles and runs the emulator)    




Sources:

RUBBoS:

http://jmob.ow2.org/rubbos.html
https://github.com/michaelmior/RUBBoS

php-cassandra-binary:
https://github.com/evseevnn/php-cassandra-binary

UUID Generator:
https://github.com/duoshuo/uuid

