<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Tweet;
use AppBundle\Form\AddUser;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $repository = $this->getDoctrine()->getRepository(User::class);

        $users = $repository->findAll();

		$form = $this->AddUserForm();
        $search = $this->AddSearchForm();


        if ($request->request->has('addUserForm')) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $task = $form->getData();
                $error = false;
                return $this->redirectToRoute('add_user', [
                    'slug' => $task['search'],
                ]);
            }
        }
        else if ($request->request->has('searchForm')) 
        {

            /*$search->handleRequest($request);

            if($search->isSubmitted() && $search->isValid()){

                $searchData = $search->getData();
                return $this->redirectToRoute('search', [
                    'keyword' => $searchData['search'],
                    'user' => $searchData['user']->getId(),
                    'page' => $searchData['page'],
                ]);
            } */

            $search->handleRequest($request);

            if ($search->isSubmitted() && $search->isValid()) {

                $data = $search->getData();
                


                
                /*return $this->render('search.html.twig', [
                    'form' => $form->createView(),
                    'searchResults' => $paginator,
                    'maxPages' => $maxPages,
                    'thisPage' => $data['page'],
                    'username' => $data['user']->getUsername() ,
                    'keyword' => $data['search'],
                    'user' => $data['user']->getId(),
                    'page' => $data['page'],
                ]);*/
                return $this->redirectToRoute('search', [
                    'keyword' => $data['search'],
                    'user' => $data['user']->getId(),
                    'page' => $data['page'],
                ]);
            }


        }
        else {
            return $this->render('default/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
                'form' => $form->createView(),
                'search' => $search->createView(),
                'users' => $users,
            ]);
        }

       
    }
	
	/**
	* Matches /*
	*
	* @Route("/search/{user}/{keyword}/{page}", name="search")
	*/
	public function search($user, $keyword, $page, Request $request)
    {
        dump($request); die;
    	/*$qb = $this->getDoctrine()->getManager()->getRepository(\AppBundle\Entity\Tweet::class)->createQueryBuilder('o');

        $qb->where('o.body LIKE :search');
        $qb->setParameter('search', '%' . $keyword . '%');

        if (1) {
            $qb->andWhere('o.user = :user');
            $qb->setParameter('user', $user);
        }

        $result = $qb
       ->getQuery()
       ->getResult();	

        $repository = $this->getDoctrine()->getRepository(User::class);


		$form = $this->AddUserForm();
		$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
			
			$task = $form->getData();
			
			return $this->redirectToRoute('add_user', [
                'slug' => $task['search'],
            ]);
		}
		else 
		{
            $user = $repository->findOneBy(array('id' => $user));

			return $this->render('search.html.twig', [
				'form' => $form->createView(),
				'results' => $result,
                'username' => $user->getUsername(),
			]);
		}*/
	//        $currentPage = $request->get('page', 1);

        $queryBuilder = $this->getDoctrine()
            ->getManager()
            ->getRepository(\AppBundle\Entity\Tweet::class)
            ->createQueryBuilder('t');

        $queryBuilder->where('t.body LIKE :search');
        $queryBuilder->setParameter('search', '%' . $keyword . '%');

        $queryBuilder->andWhere('t.user = :user');
        $queryBuilder->setParameter('user', $user);
        
        $paginator = $this->paginate($queryBuilder->getQuery(), $page);

        $limit = 5;
        $maxPages = ceil($paginator->count() / $limit);



        #$requestUri = $request->get('requestUri');

        $form = $this->AddUserForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $task = $form->getData();
            
            return $this->redirectToRoute('add_user', [
                'slug' => $task['search'],
            ]);
        }
        else 
        {

            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('id' => $user));

            return $this->redirectToRoute('search', [
                'keyword' => $keyword,
                'user' => $user->getId(),
                'page' => $page,
                'form' => $form->createView(),
                'searchResults' => $paginator,
                'maxPages' => $maxPages,
                'thisPage' => $page,
                'username' => $user->getUsername() ,
            ]);
        }
    }

    private function paginate($dql, $page = 1, $limit = 5)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
     * Matches /*
     *
     * @Route("/view/{slug}", name="view_user")
     */
    public function viewAction($slug, Request $request)
    {

        $twits = $this->showTweets($slug);

        $form = $this->AddUserForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $task = $form->getData();
            
            return $this->redirectToRoute('add_user', [
                'slug' => $task['search'],
            ]);
        }
        else 
        {
            return $this->render('view.html.twig', [
                'form' => $form->createView(),
                'tweets' => $twits,
                'username' => $slug,
            ]);
        }
    }

    /**
     * Matches /*
     *
     * @Route("/{slug}", name="add_user")
     */
    public function addAction($slug, Request $request)
    {
        $error = $this->validateUserAction($slug);
        
        $form = $this->AddUserForm();	
		$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
			
			$task = $form->getData();
			
			return $this->redirectToRoute('add_user', [
                'slug' => $task['search'],
            ]);
		}
		else 
		{
			return $this->render('add.html.twig', array(
				'slug' => $slug,
				'form' => $form->createView(),
				'error' => $error
			));
		}
    }

	
	
	
	
	public function validateUserAction($slug){
		
		$proxy = $this->get(\AppBundle\Service\TwitterProxy::class);

        if($proxy->userExists($slug))
        {
			$this->saveUserAction($slug);

            return false;
        }
        else
		{
            /*$user = new User();*/
            return true;
        }
	}
	
    public function saveUserAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $proxy = $this->get(\AppBundle\Service\TwitterProxy::class);

        $user = new User();
        $user->setUsername($slug); //$user->setUsername('webcampzagreb');
        $user->setCreated(new \DateTime());

        foreach(json_decode($proxy->getTweets($slug)) as $actualTweet) {
            $tweet = new Tweet();
            $tweet->setBody($actualTweet->text);
            $tweet->setUser($user);

            $user->addTweet($tweet);
            $em->persist($tweet);
        }

        $em->persist($user);
        $em->flush();

        return 'success';
    }
	
	public function AddUserForm()
    {
        return $this->get('form.factory')->createNamedBuilder('addUserForm')
			->setMethod('POST')
            ->add('search', TextType::class, array('label' => false, 'attr' => array('class' => 'form-control mr-sm-2')))
            ->add('save', SubmitType::class, array('label' => 'Add user', 'attr' => array('class' => 'btn btn-outline-success my-2 my-sm-0')))
            ->getForm();

    }

    public function AddSearchForm()
    {
        return $this->get('form.factory')->createNamedBuilder('searchForm', FormType::class, [],
            [
                #'method' => 'GET',
                'csrf_protection' => false,
            ]
        )
            ->add('search', TextType::class, array('label' => false, 'attr' => array('class' => 'form-control mr-sm-2')))
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add('page', HiddenType::class, ['empty_data' => 1])
            ->add('submit', SubmitType::class, array('label' => 'SEARCH', 'attr' => array('class' => 'btn btn-outline-success my-2 my-sm-0')))
            ->getForm();

    }

    public function showTweets($slug)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(array('username' => $slug));

        $tweets = $user->getTweets();

/*foreach ($tweets as $key => $value) {
            dump($value);
        }        dump($tweets); die;*/

        return $tweets;

    }
}
