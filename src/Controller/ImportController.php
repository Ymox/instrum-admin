<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\ImportType;
use App\Service\CsvMapper;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(name: "import_")]
class ImportController extends AbstractController
{
    #[Route("/import", name: "index", methods: ["GET", "POST"])]
    public function import(Request $request, FileUploader $uploader)
    {
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $type = $form->get('type')->getData();
            if ($file) {
                $fileName = $uploader->upload($file);
                $session = $request->getSession();
                $session->set('sentFile', pathinfo($file->getClientOriginalname(), PATHINFO_FILENAME));
                $session->set('savedFile', $fileName);
                $session->set('uploadTime', (new \DateTime())->format('d.m.Y à H:i'));

                return $this->redirectToRoute('import_check', ['type' => $type, 'row' => 1]);
            }
        }

        return $this->render('import/index.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Allows to handle duplicates in database
     */
    #[Route("/import/{type}/{row<\d+>}", name: "check", methods: ["GET", "POST"])]
    public function check(Request $request, string $type, int $row, TranslatorInterface $translator, EntityManagerInterface $em)
    {
        $className = preg_replace_callback('`(?:^|-)([a-z])`', function ($initial) {
            return strtoupper($initial[1]);
        }, $type);
        $repository = $em->getRepository('App\\Entity\\' . $className);
        $mapper = new CsvMapper($request->getSession()->get('savedFile'), $this->getParameter('member_csv_mappings'));
        $duplicate = $mapper->get($row);
        $match = 'full_name_birthday';
        if (!($birthday = date_create_from_format('d.m.Y', $duplicate['birthday']))
            || !($master = $repository->findBy(['firstname' => $duplicate['firstname'], 'lastname' => $duplicate['lastname'], 'birthday' => $birthday]))
            || count($master) > 1
        ) {
            if (!($master = $repository->findBy(['firstname' => $duplicate['firstname'], 'lastname' => $duplicate['lastname']])) || count($master) > 1) {
                if (!($master = $repository->findByMobile($duplicate['mobile'])) || count($master) > 1) {
                    $this->addFlash('warning', $translator->trans('app.flash.warning.import.match.member.none'));
                    $master = new Member();
                } else {
                    $match = 'mobile';
                }
            } else {
                $match = 'full_name';
            }
        }
        $master = $master[0];

        $masterForm = $this->createForm('App\\Form\\' . $className . 'Type', $master, [
            'validation_groups' => ['duplicate']
        ]);
        $masterForm
            ->remove('relateds')
            ->remove('levels')
            ->remove('statuses')
            ->remove('instruments')
        ;
        $masterForm->handleRequest($request);

        if ($masterForm->isSubmitted() && $masterForm->isValid()) {
            $em->persist($master);
            $em->flush();
            $this->addFlash(
                'success',
                $translator->trans(
                    'app.flash.success.import.member'
                )
            );
            return $this->redirectToRoute('import_check', ['type' => $type, 'row' => ++$row]);
        }
        
        $this->addFlash('info', $translator->trans('app.flash.info.import.match.member.' . $match));

        $duplicateForm = $this->createForm('App\\Form\\' . $className . 'ImportType', $duplicate, [
            'data_class' => null
        ]);

        return $this->render('import/import_check.html.twig', [
            'type' =>  $type,
            'row' => $row,
            'master' => $master,
            'master_form' => $masterForm->createView(),
            'duplicate_form' => $duplicateForm->createView(),
        ]);
    }
}