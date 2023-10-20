# SmartCampus


# Structure des branches

Le développement de notre projet est organisé en utilisant les branches Git. Voici la structure des branches que nous utilisons :

```plaintext
main
└── dev
      └── v(numVersion)/(nameFeatures)
```

* **main** : Cette branche représente la version stable et finale de l'application. Aucune commande **~~push origin main~~** ne doit être effectuée dans cette branche avant d'avoir confirmé la fin de la release développée dans la branche dev. (À l'exception de différents fichiers sans lien direct avec le code de l'application)
* **dev** : Cette branche représente l'environnement de développement d'une version en cours. Elle regroupera les fonctionnalités qui auront été approuvées sur nos branches dites de "features" afin d'aboutir à une version stable qui, une fois terminée et fonctionnelle, sera uploadée sur la branche principale **main**.
* **v\<_xVersion_\>/\<_nameFeature_\>** : Ce template de branche devra être utilisé pour chaque nouvelle fonctionnalité que vous souhaiterez développer sur la version en cours (voir page : [Règles de développement](R%C3%A8gles%20D%C3%A9veloppement)). L'objectif de cette branche est d'implémenter de nouvelles fonctionnalités de façon indépendante de la version stable pour des fusion, retour en arrière, et répartition du travail plus simple. Cela sans impacter le projet stable. Vous ne pourrez utilisez la commande **push origin dev** qu'après avoir vérifié avec l'équipe la validité de votre fonctionnalité.
