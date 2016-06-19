[1mdiff --cc app/Resources/views/emails/commitmentConfirmation.html.twig[m
[1mindex fb21284,f21667b..0000000[m
[1m--- a/app/Resources/views/emails/commitmentConfirmation.html.twig[m
[1m+++ b/app/Resources/views/emails/commitmentConfirmation.html.twig[m
[1mdiff --cc app/Resources/views/emails/commitmentConfirmation.txt.twig[m
[1mindex 71b5e8c,6903e4e..0000000[m
[1m--- a/app/Resources/views/emails/commitmentConfirmation.txt.twig[m
[1m+++ b/app/Resources/views/emails/commitmentConfirmation.txt.twig[m
[36m@@@ -1,9 -1,5 +1,8 @@@[m
[31m- {# app/Resources/views/emails/CommitmentConfirmation.txt.twig #}[m
[31m -{# app/Resources/views/emails/commitmentConfirmation.html.twig #}[m
[31m -Lieber {{ Forename }}[m
[32m +{% if Gender == 'M' %}[m
[32m +    Lieber {{ Forename }}[m
[32m +{% else %}[m
[32m +    Liebe {{ Forename }}[m
[32m +{% endif %}[m
  [m
  Du hast dich beim Event {{ Event }} als Hölfer im Ressort {{ Department }} eingetragen.[m
  [m
[1mdiff --cc src/AppBundle/Controller/EventController.php[m
[1mindex 54e9af9,6cb3541..0000000[m
[1m--- a/src/AppBundle/Controller/EventController.php[m
[1m+++ b/src/AppBundle/Controller/EventController.php[m
[36m@@@ -375,10 -370,8 +375,9 @@@[m [mclass EventController extends Controlle[m
              )[m
              ->addPart([m
                  $this->renderView([m
[31m-                     // app/Resources/views/emails/CommitmentConfirmation.txt.twig[m
[31m-                     'emails/CommitmentConfirmation.txt.twig',[m
[32m+                     'emails/commitmentConfirmation.txt.twig',[m
                      array('Forename' => $user->getForename(),[m
[32m +                        'Gender' => $user->getGender(),[m
                          'Event' => $event->getName(),[m
                          'EventID' => $event->getId(),[m
                          'EventDate' => $event->getDate(),[m
