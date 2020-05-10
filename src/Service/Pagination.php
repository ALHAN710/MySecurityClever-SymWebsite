<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Classe de pagination qui extrait toute notion de calcul et de récupération de données de nos controllers
 * 
 * Elle nécessite après instanciation qu'on lui passe l'entité sur laquelle on souhaite travailler
 */
class Pagination
{
    /**
     * Le tableau d'entitées sur lesquelles on veut effectuer une pagination
     *
     * @var string
     */
    private $entityClass = [];

    /**
     * Le nombre d'enregistrement à récupérer
     *
     * @var integer
     */
    private $limit = 10;

    /**
     * La page sur laquelle on se trouve actuellement
     *
     * @var array
     */
    private $currentPage = [1, 1];

    private $labelOrder = [];

    private $order = [];

    /**
     * Le nom de la route que l'on veut utiliser pour les boutons de la navigation
     *
     * @var string
     */
    private $route;

    /**
     * Spécifie le tab panel actif
     *
     * @var array
     */
    private $tabPanel = [];

    /**
     * Le chemin vers le template qui contient la pagination
     *
     * @var string
     */
    private $templatePath;

    /**
     * Le manager de Doctrine qui nous permet notamment de trouver le repository dont on a besoin
     *
     * @var ObjectManager
     */
    private $manager;

    /**
     * Le moteur de template Twig qui va permettre de générer le rendu de la pagination
     *
     * @var Twig\Environment
     */
    private $twig;

    /**
     * Constructeur du service de pagination qui sera appelé par Symfony
     * 
     * N'oubliez pas de configurer votre fichier services.yaml afin que Symfony sache quelle valeur
     * utiliser pour le $templatePath
     *
     * @param ObjectManager $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param string $templatePath
     */
    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        $this->route        = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager      = $manager;
        $this->twig         = $twig;
        $this->templatePath = $templatePath;
    }

    /**
     * Permet d'afficher le rendu de la navigation au sein d'un template twig !
     * 
     * On se sert ici de notre moteur de rendu afin de compiler le template qui se trouve au chemin
     * de notre propriété $templatePath, en lui passant les variables :
     * - page  => La page actuelle sur laquelle on se trouve
     * - pages => le nombre total de pages qui existent
     * - route => le nom de la route à utiliser pour les liens de navigation
     *
     * Attention : cette fonction ne retourne rien, elle affiche directement le rendu
     * 
     * @return void
     */
    public function display()
    {
        return $this->twig->display($this->templatePath, [
            'page'  => $this->currentPage[0],
            'pages' => $this->getPages(),
            'tabPanel'   => $this->tabPanel[0],
            'route' => $this->route
        ]);
    }

    public function display1()
    {
        return $this->twig->display($this->templatePath, [
            'page'  => $this->currentPage[1],
            'pages' => $this->getPages1(),
            'tabPanel'   => $this->tabPanel[1],
            'route' => $this->route
        ]);
    }

    /**
     * Permet de récupérer le nombre de pages qui existent sur une entité particulière
     * 
     * Elle se sert de Doctrine pour récupérer le repository qui correspond à l'entité que l'on souhaite
     * paginer (voir la propriété $entityClass) puis elle trouve le nombre total d'enregistrements grâce
     * à la fonction findAll() du repository
     * 
     * @throws Exception si la propriété $entityClass n'est pas configurée
     * 
     * @return int
     */
    public function getPages(): int
    {
        if (empty($this->entityClass[0])) {
            // Si il n'y a pas d'entité configurée, on ne peut pas charger le repository, la fonction
            // ne peut donc pas continuer !
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) Connaître le total des enregistrements de la table
        $repo = $this->manager->getRepository($this->entityClass[0]);
        $total = count($repo->findAll());

        // 2) Faire la division, l'arrondir et le renvoyer
        return ceil($total / $this->limit);
    }

    public function getPages1(): int
    {
        if (empty($this->entityClass[1])) {
            // Si il n'y a pas d'entité configurée, on ne peut pas charger le repository, la fonction
            // ne peut donc pas continuer !
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) Connaître le total des enregistrements de la table
        $repo = $this->manager->getRepository($this->entityClass[1]);
        $total = count($repo->findAll());

        // 2) Faire la division, l'arrondir et le renvoyer
        return ceil($total / $this->limit);
    }

    /**
     * Permet de récupérer les données paginées pour une entité spécifique
     * 
     * Elle se sert de Doctrine afin de récupérer le repository pour l'entité spécifiée
     * puis grâce au repository et à sa fonction findBy() on récupère les données dans une 
     * certaine limite et en partant d'un offset
     * 
     * @throws Exception si la propriété $entityClass n'est pas définie
     *
     * @return array
     */
    public function getData()
    {
        if (empty($this->entityClass[0])) {
            // Si il n'y a pas d'entité configurée, on ne peut pas charger le repository, la fonction
            // ne peut donc pas continuer !
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) Calculer l'offset
        $offset = $this->currentPage[0] * $this->limit - $this->limit;

        // 2) Demander aux repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entityClass[0]);
        $data = $repo->findBy([], [$this->labelOrder[0] => $this->order[0]], $this->limit, $offset);

        // 3) Renvoyer les éléments en question
        return $data;
    }

    public function getData1()
    {
        if (empty($this->entityClass[1])) {
            // Si il n'y a pas d'entité configurée, on ne peut pas charger le repository, la fonction
            // ne peut donc pas continuer !
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) Calculer l'offset
        $offset = $this->currentPage[1] * $this->limit - $this->limit;

        // 2) Demander aux repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entityClass[1]);
        $data = $repo->findBy([], [$this->labelOrder[1] => $this->order[1]], $this->limit, $offset);

        // 3) Renvoyer les éléments en question
        return $data;
    }

    /**
     * Permet de choisir un template de pagination
     *
     * @param string $templatePath
     * @return self
     */
    public function setTemplatePath($templatePath): self
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    /**
     * Permet de récupérer le templatePath actuellement utilisé
     *
     * @return string
     */
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * Permet de changer la route par défaut pour les liens de la navigation
     *
     * @param string $route Le nom de la route à utiliser
     * @return self
     */
    public function setRoute($route): self
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Permet de récupérer le nom de la route qui sera utilisé sur les liens de la navigation
     *
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    public function setOrder($order)
    {
        $this->order[0] = $order[0];
        $this->order[1] = $order[1];

        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setLabelOrder($labelOrder)
    {
        $this->labelOrder[0] = $labelOrder[0];
        $this->labelOrder[1] = $labelOrder[1];

        return $this;
    }

    public function getLabelOrder()
    {
        return $this->labelOrder;
    }

    /**
     * Permet de spécifier la page que l'on souhaite afficher
     *
     * @param array $page
     * @return self
     */
    public function setCurrentPage($currentPage): self
    {
        $this->currentPage[0] = $currentPage[0];
        $this->currentPage[1] = $currentPage[1];

        return $this;
    }

    /**
     * Permet de récupérer la page qui est actuellement affichée
     *
     * @return array
     */
    public function getCurrentPage(): array
    {
        return $this->currentPage;
    }

    /**
     * Permet de spécifier le nombre d'enregistrements que l'on souhaite obtenir !
     *
     * @param int $limit
     * @return self
     */
    public function setLimit($limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Permet de récupérer le nombre d'enregistrements qui seront renvoyés
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Permet de spécifier l'entité sur laquelle on souhaite paginer
     * Par exemple :
     * - App\Entity\User::class
     * - App\Entity\Devices::class
     *
     * @param string $entityClass
     * @return self
     */
    public function setEntityClass($entityClass): self
    {
        $this->entityClass[0] = $entityClass[0];
        $this->entityClass[1] = $entityClass[1];

        return $this;
    }

    /**
     * Permet de récupérer les entités sur lesquelles on est en train de paginer
     *
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * Get spécifie le tab panel actif
     *
     * @return  array
     */
    public function getTabPanel(): array
    {
        return $this->tabPanel;
    }

    /**
     * Set spécifie le tab panel actif
     *
     * @param  array  $tabPanel  Spécifie le tab panel actif
     *
     * @return  self
     */
    public function setTabPanel(array $tabPanel)
    {
        $this->tabPanel[0] = $tabPanel[0];
        $this->tabPanel[1] = $tabPanel[1];

        return $this;
    }
}
