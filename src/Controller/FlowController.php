<?php
namespace App\Controller;

use App\Entity\Flow;
use App\Service\Mindmap2Botman;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class FlowController extends AbstractController
{
    /** @var  Mindmap2Botman */
    private $mindmap2Botman;

    /**
     * FlowController constructor.
     * @param Mindmap2Botman $mindmap2Botman
     */
    public function __construct(Mindmap2Botman $mindmap2Botman)
    {
        $this->mindmap2Botman = $mindmap2Botman;
    }


    /**
     * @Route("/flows/list", name="flows_list")
     */
    public function listFlows()
    {
        return new Response('');
    }

    /**
     * @Route("/flows/create", name="flows_create")
     */
    public function createFlow(Request $request)
    {
        $flow = new Flow();

        $form = $this->createFormBuilder($flow)
            ->add('name', TextType::class)
            ->add('flow', HiddenType::class)
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $flow = $form->getData();
            $this->getDoctrine()->getManager()->persist($flow);
            $this->getDoctrine()->getManager()->flush();
            $this->mindmap2Botman->generate($flow);

            return $this->redirectToRoute('flows_edit', ['id'=>$flow->getId()]);
        }

        return $this->render('flow/edit.html.twig',['flow'=>$flow, 'form'=>$form->createView()]);
    }

    /**
     * @Route("/flows/edit/{id}", name="flows_edit")
     */
    public function editFlow(Request $request, $id)
    {
        $flow = $this->getDoctrine()->getRepository(Flow::class)->find($id);

        $form = $this->createFormBuilder($flow)
            ->add('name', TextType::class)
            ->add('flow', HiddenType::class)
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $flow = $form->getData();
            $this->getDoctrine()->getManager()->persist($flow);
            $this->getDoctrine()->getManager()->flush();
            $this->mindmap2Botman->generate($flow);
        }

        return $this->render('flow/edit.html.twig',['flow'=>$flow, 'form'=>$form->createView()]);
    }
}