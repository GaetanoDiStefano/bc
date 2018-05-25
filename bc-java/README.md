To start:
- give read/write permission to bc-java dir and subdirs: chmod -R a+rw bc-java/* 
- cd in bc-java directory
- download gson-2.8.2.jar and put it in a known directory ex: /home/user/jar 
- compile java with this command:
    - $ javac -cp ":/home/user/jar/gson-2.8.2.jar" noobchain/*.java
- execute with this command:
    - $ java -cp ":/home/user/jar/gson-2.8.2.jar" noobchain.NoobChain
    
will create  bc1_java.txt with the json format of blockchain
