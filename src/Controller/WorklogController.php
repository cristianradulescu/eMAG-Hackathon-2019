<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\Worklog\Connectors\BugTracker;
use App\Service\Worklog\Connectors\Calendar;
use App\Service\Worklog\Connectors\Vcs;
use App\Service\Worklog\DataSource\FileDataSource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/worklog")
 */
class WorklogController extends AbstractController
{
    /** @var Calendar  */
    private $calendarConnector;

    /** @var BugTracker  */
    private $bugTrackerConnector;

    /** @var Vcs  */
    private $vcsConnector;

    public function __construct(Calendar $calendarConnector, BugTracker $bugTrackerConnector, Vcs $vcsConnector)
    {
        $this->calendarConnector = $calendarConnector;
        $this->bugTrackerConnector = $bugTrackerConnector;
        $this->vcsConnector = $vcsConnector;
    }

    /**
     * @Route(path="/suggest")
     */
    public function suggestWorklog(): Response
    {
        $alreadyLogged = $this->bugTrackerConnector->setDataSource(new FileDataSource(__DIR__.'/../../public/data/jira.json'))->getData();
        $meetings = $this->calendarConnector->setDataSource(new FileDataSource(__DIR__.'/../../public/data/calendar.json'))->getData();
        $commits = $this->vcsConnector->setDataSource(new FileDataSource(__DIR__.'/../../public/data/stash.json'))->getData();

        return $this->render(
            'worklog/index.html.twig',
            [
                'logged' => $alreadyLogged,
                'meetings' => $meetings,
                'commits' => $commits,
            ]
        );
    }
}