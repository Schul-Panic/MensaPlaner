<?php

namespace App\Controller;

use App\Model\Dish;
use App\Model\Order;
use App\Model\Vote;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DishController
{
    public function showDishes(Request $request, Response $response): Response
    {
        $dishModel = new Dish();
        $weeklyMatrix = $dishModel->getWeeklyMatrix();
        $today = $this->currentGermanWeekday();
        $monday = $this->mondayOfWeek(0);
        $weekLabel = $this->weekLabel($monday);
        $weekdayDates = $this->weekdayDates($monday);

        ob_start();
        require __DIR__ . '/../View/dishes.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    public function showNextWeekDishes(Request $request, Response $response): Response
    {
        $dishModel = new Dish();
        $weeklyMatrix = $dishModel->getNextWeekMatrix();
        $monday = $this->mondayOfWeek(1);
        $weekLabel = $this->weekLabel($monday);
        $weekdayDates = $this->weekdayDates($monday);

        ob_start();
        require __DIR__ . '/../View/dishes-next-week.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    public function addToCart(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $day = $data['day'] ?? '';
        $row = $data['row'] ?? '';
        $column = $data['column'] ?? '';

        $dish = (new Dish())->getNextWeekMatrix()[$day][$row][$column] ?? null;

        if ($dish) {
            $key = "{$day}|{$row}|{$column}";
            $_SESSION['cart'][$key] = ($_SESSION['cart'][$key] ?? 0) + 1;
        }

        return $response->withHeader('Location', '/speiseplan/naechste')->withStatus(302);
    }

    public function removeFromCart(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $key = ($data['day'] ?? '') . '|' . ($data['row'] ?? '') . '|' . ($data['column'] ?? '');

        unset($_SESSION['cart'][$key]);

        return $response->withHeader('Location', '/speiseplan/warenkorb')->withStatus(302);
    }

    public function showCart(Request $request, Response $response): Response
    {
        [$cartItems, $total] = $this->resolveCartItems();
        $totalFormatted = number_format($total, 2, ',', '.') . ' €';

        ob_start();
        require __DIR__ . '/../View/cart.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    public function placeOrder(Request $request, Response $response): Response
    {
        [$cartItems, $total] = $this->resolveCartItems();

        if ($cartItems) {
            (new Order())->create((int) $_SESSION['account_id'], $cartItems);
        }

        unset($_SESSION['cart']);

        ob_start();
        $orderPlaced = true;
        $totalFormatted = number_format($total, 2, ',', '.') . ' €';
        $cartItems = [];
        require __DIR__ . '/../View/cart.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    private function resolveCartItems(): array
    {
        $matrix = (new Dish())->getNextWeekMatrix();
        $items = [];
        $total = 0.0;

        foreach ($_SESSION['cart'] ?? [] as $key => $quantity) {
            [$day, $row, $column] = explode('|', $key);
            $dish = $matrix[$day][$row][$column] ?? null;

            if (!$dish) {
                continue;
            }

            $unitPrice = (float) str_replace(',', '.', rtrim(trim($dish['price']), ' €'));
            $lineTotal = $unitPrice * $quantity;
            $total += $lineTotal;

            $items[] = [
                'day' => $day,
                'row' => $row,
                'column' => $column,
                'name' => $dish['name'],
                'price' => $dish['price'],
                'quantity' => $quantity,
                'lineTotal' => number_format($lineTotal, 2, ',', '.') . ' €',
            ];
        }

        return [$items, $total];
    }

    public function showOrderOverview(Request $request, Response $response): Response
    {
        $orderModel = new Order();
        $categoryMap = (new Dish())->getDishCategoryMap();
        $quantities = $orderModel->quantitiesByDishName();

        $dishesByCategory = array_fill_keys(array_keys(Dish::ROW_LABELS), []);
        $uncategorized = [];

        foreach ($quantities as $dishName => $quantity) {
            $category = $categoryMap[$dishName] ?? null;
            $entry = ['name' => $dishName, 'quantity' => $quantity];

            if ($category === null) {
                $uncategorized[] = $entry;
                continue;
            }

            $dishesByCategory[$category][] = $entry;
        }

        $sortByQuantityDesc = fn ($a, $b) => $b['quantity'] <=> $a['quantity'];

        foreach ($dishesByCategory as &$dishes) {
            usort($dishes, $sortByQuantityDesc);
        }
        unset($dishes);

        usort($uncategorized, $sortByQuantityDesc);

        $topDish = null;
        foreach ($quantities as $dishName => $quantity) {
            if ($topDish === null || $quantity > $topDish['quantity']) {
                $topDish = ['name' => $dishName, 'quantity' => $quantity];
            }
        }

        $maxQuantity = $quantities ? max($quantities) : 0;
        $totalItems = array_sum($quantities);
        $totalOrders = $orderModel->totalCount();

        ob_start();
        require __DIR__ . '/../View/orders.php';
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

        $allDishIds = array_column($dishModel->getFlattenedDishes(), 'id');
        $voteModel = new Vote();
        $votes = $voteModel->resultsForDishIds($allDishIds);
        $votedChoices = $voteModel->userVotes((int) $_SESSION['account_id']);
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
            (new Vote())->cast($dishId, (int) $_SESSION['account_id'], $direction);
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
