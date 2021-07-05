<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* themes/custom/bcgov_teachers/templates/layout/page.html.twig */
class __TwigTemplate_d3b6f6779d5b30dd354df6f5e6282e713198be07f6c42c62fac1f98f1821d9dc extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 46
        $context["nav_classes"] = "navbar navbar-expand-md navbar-dark bc-navbar";
        // line 48
        echo "
";
        // line 50
        $context["footer_classes"] = " ";
        // line 52
        echo "
<header>
  ";
        // line 54
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "header", [], "any", false, false, true, 54), 54, $this->source), "html", null, true);
        echo "

  ";
        // line 56
        if (((twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "nav_branding", [], "any", false, false, true, 56) || twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "nav_main", [], "any", false, false, true, 56)) || twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "nav_additional", [], "any", false, false, true, 56))) {
            // line 57
            echo "  <nav class=\"";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["nav_classes"] ?? null), 57, $this->source), "html", null, true);
            echo "\">
    <div class=\"container row mx-auto\">
      <div class=\"mobile-search col-auto navbar-toggler\">
        ";
            // line 60
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "nav_branding", [], "any", false, false, true, 60), 60, $this->source), "html", null, true);
            echo "
      </div>

      <div class=\"mobile-menu-toggle col-auto text-right\">
        <button class=\"navbar-toggler collapsed\" type=\"button\" data-toggle=\"collapse\"
                data-target=\"#navbarSupportedContent\" aria-controls=\"navbarSupportedContent\"
                aria-expanded=\"false\" aria-label=\"Toggle navigation\">
          <span class=\"navbar-toggler-icon\"></span>
        </button>
      </div>

      <div class=\"collapse navbar-collapse col-12 col-md-auto p-0 justify-content-start\" id=\"navbarSupportedContent\">
        ";
            // line 72
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "nav_main", [], "any", false, false, true, 72), 72, $this->source), "html", null, true);
            echo "
        ";
            // line 73
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "nav_additional", [], "any", false, false, true, 73), 73, $this->source), "html", null, true);
            echo "
      </div>
    </div>
  </nav>
  ";
        }
        // line 78
        echo "
</header>

<main role=\"main\">
  <a id=\"main-content\" tabindex=\"-1\"></a>";
        // line 83
        echo "
  ";
        // line 85
        $context["sidebar_first_classes"] = (((twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_first", [], "any", false, false, true, 85) && twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_second", [], "any", false, false, true, 85))) ? ("col-12 col-sm-6 col-lg-3") : ("col-12 col-lg-3"));
        // line 87
        echo "
  ";
        // line 89
        $context["sidebar_second_classes"] = (((twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_first", [], "any", false, false, true, 89) && twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_second", [], "any", false, false, true, 89))) ? ("col-12 col-sm-6 col-lg-3") : ("col-12 col-lg-3"));
        // line 91
        echo "
  ";
        // line 93
        $context["content_classes"] = (((twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_first", [], "any", false, false, true, 93) && twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_second", [], "any", false, false, true, 93))) ? ("col-12 col-lg-6") : ((((twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_first", [], "any", false, false, true, 93) || twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_second", [], "any", false, false, true, 93))) ? ("col-12 col-lg-9") : ("col-12"))));
        // line 95
        echo "
  ";
        // line 96
        if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "breadcrumb", [], "any", false, false, true, 96)) {
            // line 97
            echo "    ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "breadcrumb", [], "any", false, false, true, 97), 97, $this->source), "html", null, true);
            echo "
  ";
        }
        // line 99
        echo "  <div class=\"row no-gutters\">
    ";
        // line 100
        if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_first", [], "any", false, false, true, 100)) {
            // line 101
            echo "      <div class=\"order-2 order-lg-1 ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["sidebar_first_classes"] ?? null), 101, $this->source), "html", null, true);
            echo "\">
        ";
            // line 102
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_first", [], "any", false, false, true, 102), 102, $this->source), "html", null, true);
            echo "
      </div>
    ";
        }
        // line 105
        echo "    <div class=\"order-1 order-lg-2 ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["content_classes"] ?? null), 105, $this->source), "html", null, true);
        echo "\">
      ";
        // line 106
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 106), 106, $this->source), "html", null, true);
        echo "
    </div>
    ";
        // line 108
        if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_second", [], "any", false, false, true, 108)) {
            // line 109
            echo "      <div class=\"order-3 ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["sidebar_second_classes"] ?? null), 109, $this->source), "html", null, true);
            echo "\">
        ";
            // line 110
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_second", [], "any", false, false, true, 110), 110, $this->source), "html", null, true);
            echo "
      </div>
    ";
        }
        // line 113
        echo "  </div>

</main>

";
        // line 117
        if ((twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer", [], "any", false, false, true, 117) || twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "under_footer", [], "any", false, false, true, 117))) {
            // line 118
            echo "  <footer class=\"mt-auto ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["footer_classes"] ?? null), 118, $this->source), "html", null, true);
            echo "\">
    ";
            // line 119
            if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer", [], "any", false, false, true, 119)) {
                // line 120
                echo "      <div class=\"footer\">
        <div class=\"container\">
          ";
                // line 122
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer", [], "any", false, false, true, 122), 122, $this->source), "html", null, true);
                echo "
        </div>
      </div>
    ";
            }
            // line 126
            echo "
    ";
            // line 127
            if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "under_footer", [], "any", false, false, true, 127)) {
                // line 128
                echo "      <div class=\"container\">
        ";
                // line 129
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "under_footer", [], "any", false, false, true, 129), 129, $this->source), "html", null, true);
                echo "
      </div>
    ";
            }
            // line 132
            echo "  </footer>
";
        }
    }

    public function getTemplateName()
    {
        return "themes/custom/bcgov_teachers/templates/layout/page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  202 => 132,  196 => 129,  193 => 128,  191 => 127,  188 => 126,  181 => 122,  177 => 120,  175 => 119,  170 => 118,  168 => 117,  162 => 113,  156 => 110,  151 => 109,  149 => 108,  144 => 106,  139 => 105,  133 => 102,  128 => 101,  126 => 100,  123 => 99,  117 => 97,  115 => 96,  112 => 95,  110 => 93,  107 => 91,  105 => 89,  102 => 87,  100 => 85,  97 => 83,  91 => 78,  83 => 73,  79 => 72,  64 => 60,  57 => 57,  55 => 56,  50 => 54,  46 => 52,  44 => 50,  41 => 48,  39 => 46,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/custom/bcgov_teachers/templates/layout/page.html.twig", "/var/www/sites/mst-dev/src/web/themes/custom/bcgov_teachers/templates/layout/page.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 46, "if" => 56);
        static $filters = array("escape" => 54);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
