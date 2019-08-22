<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class UserController extends AbstractFOSRestController {

    private $userRepository;

    public function __construct(UserRepository $userRepository)   
    {
        $this->userRepository = $userRepository;   
    }
    
    /**    
     * @Rest\Get("/api/users/{email}")    
     */
    public function getApiUser(User $user)
    {
      return $this->view($user);
    }
    

    
    /**    
     * @Rest\Post("/api/users") 
     * @ParamConverter("user", converter="fos_rest.request_body")   
     */
    public function postApiUser(User $user, ConstraintViolationListInterface $validationErrors)
    {
      $errors = array();
      if ($validationErrors->count() > 0) 
      {
        /** 
         * @var ConstraintViolation $constraintViolation 
         */
        foreach ($validationErrors as $constraintViolation)
        {
          // Returns the violation message. (Ex. This value should not be blank.)
          $message = $constraintViolation->getMessage();
          // Returns the property path from the root element to the violation. (Ex. lastname)
          $propertyPath = $constraintViolation->getPropertyPath();
          $errors[] = ['message' => $message, 'propertyPath' => $propertyPath];
        }   
      }
      if (!empty($errors)) 
      {
        // Throw a 400 Bad Request with all errors messages (Not readable, you can do better)
        throw new BadRequestHttpException(\json_encode($errors));
    }
    $this->em->persist($user);
    $this->em->flush();
    return $this->view($user);
  }
    /**  
     * @Rest\Patch("/api/users/{email}")  
     * @Rest\View(serializerGroups={"user"})
     */
    public function patchApiUser(User $user, Request $request, ValidatorInterface $validator)
    {
      
      $validationErrors = $validator->validate($user);
      if ($validationErrors->count() > 0) 
      {
        // Same validator as POST
      
      }
      $user->_($request->get('email'));
      $attributes = ['firstname' => 'setFirstname'];
      foreach($attributes as $attributesName => $setterName){
        if($request->get($attributesName) === null){
           continue;
        }

        $user->$setterName($request->request->get($attributes));
      }
      $this->entityManager->flush();
    }
    
    /**    
     * @Rest\Delete("/api/users/{email}")    
     */
    public function deleteApiUser(User $user){}
    
    }
