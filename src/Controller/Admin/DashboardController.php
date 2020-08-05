<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\UserEvent;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('IciOnParle')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoRoute('IciOnParle', 'fa fa-comments', 'app_dashboard');
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('User', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Event', 'fa fa-calendar-day', Event::class);
        yield MenuItem::linkToCrud('Event', 'fa fa-user-plus', UserEvent::class);
    }
}
