#BPMN E PROCESSOS GERENCIAIS

    Tema: BPMN E PROCESSOS GERENCIAIS
    
    Aluno: André Luiz Lunelli 
    
    Tecnologia(s): PHP, Doctrine, SlimFramework, PHPUnit, Webpack, TYPESCRIPT/JAVASCRIPT, HTML, BPMN.io
   
##Pré requisitos para executar aplicação
* PHP 7.3 instalado
* composer instalado
* PostgreSql 9.6 instalado

##Como executar o projeto 
* criar a base de dados, alterar as configurações de acesso a base de dados
no arquivo `/src/settings.php`
* para criar as tabelas do banco, deve-se executar o comando `"vendor/bin/doctrine" orm:schema-tool:create`
dentro da pasta raiz do projeto `/`
* adicionar um usuário na base de dados com o comando a baixo
    
```
Adicionar usuário:
composer run add-user -- -n andre -e to.lunelli@gmail.com -s suasenha
```
* executar `server.bat` 

#####Caso arquivo .bat não funcione, pode executar manualmente os comandos abaixo
* dentro da pasta `/public` executar o comando `php -S localhost:1234` a porta 
pode ser alterada conforme a disponibilidade. Caso deseje disponibilizar acesso na rede,
deve-se informar o ip da maquina ao invés de localhost
* agora é só acessar http://localhost:1234
