<?php

namespace App\Controller;

use App\Model\Dish;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DishController
{
    public function showDishes(Request $request, Response $response): Response
    {
        $dishModel = new Dish();
        $weeklyMenu = $dishModel->getWeeklyMenu();
        $today = $this->currentGermanWeekday();
        $monday = $this->mondayOfWeek(0);
        $weekLabel = $this->weekLabel($monday);
        $weekdayDates = $this->weekdayDates($monday);
        $activeTab = 'current';

        ob_start();
        require __DIR__ . '/../View/dishes.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    public function showNextWeekDishes(Request $request, Response $response): Response
    {
        $dishModel = new Dish();
        $weeklyMenu = $dishModel->getWeeklyMenu();
        $monday = $this->mondayOfWeek(1);
        $weekLabel = $this->weekLabel($monday);
        $weekdayDates = $this->weekdayDates($monday);
        $activeTab = 'next';

        ob_start();
        require __DIR__ . '/../View/dishes-next-week.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    public function showNextWeekVoting(Request $request, Response $response): Response
    {
        $dishModel = new Dish();
        $dishesByCategory = array_fill_keys(array_keys(Dish::CATEGORY_LABELS), []);

        foreach ($dishModel->getFlattenedDishes() as $dish) {
            $dishesByCategory[$dish['category']][] = $dish;
        }

        foreach ($dishesByCategory as &$dishes) {
            usort($dishes, fn ($a, $b) => $a['vegan'] <=> $b['vegan']);
        }
        unset($dishes);

        $votes = $_SESSION['votes'] ?? [];
        $weekLabel = $this->weekLabel($this->mondayOfWeek(1));

        ob_start();
        require __DIR__ . '/../View/next-week.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    public function voteDish(Request $request, Response $response, array $args): Response
    {
        $dishId = (int) $args['id'];
        $direction = $args['direction'] ?? '';

        if (in_array($direction, ['up', 'down'], true)) {
            $_SESSION['votes'][$dishId][$direction] = ($_SESSION['votes'][$dishId][$direction] ?? 0) + 1;
        }

        return $response->withHeader('Location', '/speiseplan/naechste-woche')->withStatus(302);
    }

    private function currentGermanWeekday(): string
    {
        $weekdays = [
            "Monday" => "Montag",
            "Tuesday" => "Dienstag",
            "Wednesday" => "Mittwoch",
            "Thursday" => "Donnerstag",
            "Friday" => "Freitag",
            "Saturday" => "Samstag",
            "Sunday" => "Sonntag",
        ];

        return $weekdays[date("l")];
    }

    private function mondayOfWeek(int $weekOffset): DateTime
    {
        $monday = new DateTime();
        $monday->modify('-' . ((int) $monday->format('N') - 1) . ' days');
        $monday->modify($weekOffset * 7 . ' days');

        return $monday;
    }

    private function weekLabel(DateTime $monday): string
    {
        $friday = (clone $monday)->modify('+4 days');

        return sprintf(
            '(KW %s %s-%s)',
            $monday->format('W'),
            $monday->format('d.m.y'),
            $friday->format('d.m.y')
        );
    }

    private function weekdayDates(DateTime $monday): array
    {
        $germanMonths = [
            1 => "Januar", 2 => "Februar", 3 => "März", 4 => "April",
            5 => "Mai", 6 => "Juni", 7 => "Juli", 8 => "August",
            9 => "September", 10 => "Oktober", 11 => "November", 12 => "Dezember",
        ];

        $weekdayNames = ["Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag"];
        $dates = [];

        foreach ($weekdayNames as $offset => $weekdayName) {
            $date = (clone $monday)->modify("+{$offset} days");
            $dates[$weekdayName] = sprintf('%d. %s', (int) $date->format('j'), $germanMonths[(int) $date->format('n')]);
        }

        return $dates;
    }
}
