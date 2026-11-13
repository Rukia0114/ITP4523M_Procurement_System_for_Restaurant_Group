<?php
/**
 * View Base Class
 * Provides common layout and rendering functionality
 */

abstract class View {
    protected array $data = [];
    protected string $layout = 'default';
    
    public function __construct() {
        // Auto-load config
        if (empty($this->data['auth'])) {
            $this->data['auth'] = Auth::getInstance();
        }
    }

    /**
     * Set view data
     */
    public function with(string|array $key, mixed ...$value): self {
        foreach ($value as $val) {
            if (is_string($key) && is_array($val)) {
                $this->data[$key] = $val;
            } elseif (is_array($key)) {
                $this->data += $key;
            } else {
                $this->data[$key] = $val;
            }
        }
        return $this;
    }

    /**
     * Render view with layout
     */
    public function render(string $template, string $layout = 'default'): string {
        extract($this->data);
        
        $viewPath = __DIR__ . "/../views/{$layout}/{$template}.php";
        $layoutPath = __DIR__ . "/../layouts/{$layout}.php";

        if (!file_exists($viewPath)) {
            $viewPath = __DIR__ . "/../views/{$template}.php";
        }

        ob_start();

        if (file_exists($viewPath)) {
            require $viewPath;
        }

        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $this->getRawContent();
        }

        return ob_get_clean();
    }

    /**
     * Get raw content before layout
     */
    protected function getRawContent(): string {
        extract($this->data);
        ob_start();
        
        if (file_exists(__DIR__ . '/../views/' . ($this->data['template'] ?? 'default') . '.php')) {
            require __DIR__ . '/../views/' . ($this->data['template'] ?? 'default') . '.php';
        }

        return ob_get_clean();
    }

    /**
     * Extension point for child classes
     */
    abstract protected function buildContent(): void;

    /**
     * Common navigation helper for managers
     */
    protected function renderManagerNav(): string {
        global $auth, $userID, $userType;
        
        ob_start();
        ?>
        <nav>
            <div class="container">
                <ul id="menu" class="nav justify-content-end mb-3">
                    <li><a href="makeOrder.php" class="head">Make Order</a></li>
                    <li><a href="viewOrders.php" class="head">View Orders</a></li>
                    <li><a href="Report.php" class="head">Report</a></li>
                    <li><a href="logout.php" class="head" id="logout">Logout</a></li>
                    <li><a id="Name"><?php echo "$userID - $auth->getUsername()"; ?></a></li>
                </ul>
            </div>
        </nav>
        <?php
        return ob_get_clean();
    }

    /**
     * Common navigation helper for suppliers
     */
    protected function renderSupplierNav(): string {
        global $auth, $userID, $userType;
        
        ob_start();
        ?>
        <nav>
            <div class="container">
                <ul id="menu" class="nav justify-content-end mb-3">
                    <li><a href="Insert.php" class="head">Insert Item</a></li>
                    <li><a href="viewItem.php" class="head">View Item</a></li>
                    <li><a href="Report.php" class="head">Report</a></li>
                    <li><a href="Delete.php" class="head">Delete Item Records</a></li>
                    <li><a href="logout.php" class="head" id="logout">Logout</a></li>
                    <li><a id="Name"><?php echo "$userID - $auth->getUsername()"; ?></a></li>
                </ul>
            </div>
        </nav>
        <?php
        return ob_get_clean();
    }
}
