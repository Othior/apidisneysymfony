<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Personnage;
use App\Repository\PersonnageRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PersonnageController extends AbstractController
{    
    #[Route('/personnage_post', name: 'personnage_post' , methods:"post")]
    public function createPersonnage(Request $resquest,ValidatorInterface $validator,SerializerInterface $serializer,EntityManagerInterface $entityManager): Response {
        

        $jsonRecu = $resquest->getContent();

        try{

            $person = $serializer->deserialize($jsonRecu,Personnage::class,'json');

            $errors = $validator->validate($person);

            if(count($errors) > 0){
                return $this->json($errors,400);
            }

            $entityManager->persist($person);
            $entityManager->flush();

            return $this->json($person,201,[],['groups' => 'perso' ]);

        }catch(NotEncodableValueException $e){
            return $this->json(['status' => 400,"message" => $e->getMessage()],400);
        }

        return new Response("Saved new personnage with id ".$person->getId());
    }

    #[Route('/personnage/{id}', name: 'personnage_name' , methods:"get")]
    public function getPersonnage(int $id,PersonnageRepository $personnageRepository): Response {
        
        $person = $personnageRepository->find($id);

        return $this->json($person,200,[],['groups' => 'perso' ]);

    }


    #[Route('/personnage', name:'personnage_all' , methods:"get")]
    public function getAllPersonnage(PersonnageRepository $personnageRepository): Response{
        
        $person = $personnageRepository->findAll();
        
        return $this->json($person,200,[],['groups' => 'perso' ]);
        
    }
    
}
