To start:
- give read/erite permission to bc-java dir and subdirs: chmod -R a+rw bc-java/* 
- cd in bc-java directory
- explode gson-2.8.2.jar. Now you have com directory
- compile java with this command:
    - $ javac -cp gson-2.8.2.jar noobchain/*.java
- execute with this command:
    - $ java noobchain.NoobChain
    
will create  bc1_java.txt with the json format of blockchain
