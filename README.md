# Rapport Grégory MAJSTOROVIC

## Tests unitaires

Avant de commencer j'ai tester le projet actuel :

![alt text](/images-rapport/image.png)

les tests sont concluants.

## Test web

L'interface web fonctionne correctement, je vois les tâches.

![alt text](/images-rapport/image6.png)

## Preuve mise à l'échelle et limite de ressource

On voit bien les 3 réplicas et qu'ils sont plafonnées à 128MB.

![alt text](/images-rapport/image8.png)

## Test cloisonnement réseau

Quand je tente de faire `docker compose exec web ping db`, il me répond `ping: bad address 'db'`, ce qui prouve que le conteneur front n'a pas accès à la base de données.

![alt text](/images-rapport/image2.png)

## Bonne version image

J'ai bien utilisé les sha pour toutes les images comme ça je suis sûr d'avoir la bonne version

![alt text](/images-rapport/image3.png)

## Test CI/CD

J'ai changé le code pour que le test renvoie une erreur, et on l'a voit bien dans les tests du CI CD.

![alt text](/images-rapport/image4.png)

Et là quand je remet le code correct, on voit bien que ça fonctionne correctement.

![alt text](/images-rapport/image5.png)

## Image sur Docker Hub

L'image est bien poussée sur docker hub
https://hub.docker.com/repository/docker/misteraxolotl/tp-msii-api/general

![alt text](/images-rapport/image7.png)