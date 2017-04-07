#Usage

The bundle provide a new command 

    php bin/console victoire:blog-import
    
When using it, the command will start in interactive mod.
It require some parameter:

| option                      | shortcut | definition                                                                                                | required |
|-----------------------------|----------|-----------------------------------------------------------------------------------------------------------|----------|
| blog-name                   | -b       | define the new victoire blog name                                                                         | true     |
| blog-template               | -bt      | id of the base template for the new blog                                                                  | true     |
| blog-parent-id              | -bpi     | id of the blog parent pages                                                                               | true     |
| dump                        | -d       | Path to the xml source file                                                                               | true     |
| article-template-id         | -ati     | If you want to use an existing ArticleTemplate Id                                                         | false    |
| article-template-name       | -atn     | If you dont use an existing ArticleTemplate  you have to provide a name for the new one                   | false    |
| article-template-layout     | -atl     | If you dont have an existing ArticleTemplate,  you have to provide the layout designation for the new one | false    |
| article-template-parent-id  | -atpid   | If you dont have an existing ArticleTemplate,  you have to provide the parent id for the new one.         | false    |
| article-template-first-slot | -atfs    | In every case you have to define the lot definition for  your article content                             | true     |

When the command is execute if you have some blog author link to your article in your 
dump, and they have no equivalent in your bdd (an equivalent would be an user with the same email 
or username than your blog author).

Then you will get an error associate with array containing every missing author from you bdd, you have to add it 
in your bdd to execute a successful import.

Add the end of the command if every stages has return a success then you have to 
regenerate your view references manually

    php bin/console victoire:viewReference:generate