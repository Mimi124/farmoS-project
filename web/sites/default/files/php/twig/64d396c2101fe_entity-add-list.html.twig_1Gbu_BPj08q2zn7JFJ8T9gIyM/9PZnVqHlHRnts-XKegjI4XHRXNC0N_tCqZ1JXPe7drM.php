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

/* themes/gin/templates/admin/entity-add-list.html.twig */
class __TwigTemplate_3183612eda9a50189d374d1be22b0548 extends \Twig\Template
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
        // line 19
        $context["item_classes"] = [0 => "admin-item"];
        // line 23
        if ( !twig_test_empty(($context["bundles"] ?? null))) {
            // line 24
            echo "  <div class=\"panel gin-layer-wrapper\">
    ";
            // line 25
            if (($context["title"] ?? null)) {
                // line 26
                echo "      <h3 class=\"panel__title\">";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title"] ?? null), 26, $this->source), "html", null, true);
                echo "</h3>
    ";
            }
            // line 28
            echo "
    <div";
            // line 29
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => "admin-list", 1 => "panel__content", 2 => "gin-layer-wrapper"], "method", false, false, true, 29), 29, $this->source), "html", null, true);
            echo ">
      ";
            // line 30
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["bundles"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["bundle"]) {
                // line 31
                echo "        ";
                // line 35
                echo "        ";
                $context["bundle_attributes"] = ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["bundle"], "add_link", [], "any", false, false, true, 35), "url", [], "any", false, false, true, 35), "getOption", [0 => "attributes"], "method", false, false, true, 35)) ? (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["bundle"], "add_link", [], "any", false, false, true, 35), "url", [], "any", false, false, true, 35), "getOption", [0 => "attributes"], "method", false, false, true, 35)) : ([]));
                // line 36
                echo "        ";
                $context["link_attributes"] = twig_get_attribute($this->env, $this->source, $this->extensions['Drupal\Core\Template\TwigExtension']->createAttribute($this->sandbox->ensureToStringAllowed(($context["bundle_attributes"] ?? null), 36, $this->source)), "addClass", [0 => "admin-item__link"], "method", false, false, true, 36);
                // line 37
                echo "        ";
                $context["description_id"] = (\Drupal\Component\Utility\Html::getId($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["bundle"], "add_link", [], "any", false, false, true, 37), "text", [], "any", false, false, true, 37), 37, $this->source)) . "-desc");
                // line 38
                echo "        <div";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->createAttribute(["class" => ($context["item_classes"] ?? null)]), "html", null, true);
                echo ">
          <a class=\"admin-item__link\" title=\"";
                // line 39
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["bundle"], "add_link", [], "any", false, false, true, 39), "text", [], "any", false, false, true, 39), 39, $this->source), "html", null, true);
                echo "\" href=\"";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["bundle"], "add_link", [], "any", false, false, true, 39), "url", [], "any", false, false, true, 39), 39, $this->source), "html", null, true);
                echo "\"></a>
          <div class=\"admin-item__title\"";
                // line 40
                if (twig_get_attribute($this->env, $this->source, $context["bundle"], "description", [], "any", false, false, true, 40)) {
                    echo " aria-details=\"";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["description_id"] ?? null), 40, $this->source), "html", null, true);
                    echo "\"";
                }
                echo ">
            ";
                // line 41
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["bundle"], "add_link", [], "any", false, false, true, 41), "text", [], "any", false, false, true, 41), 41, $this->source), "html", null, true);
                echo "
          </div>
          ";
                // line 44
                echo "          ";
                if (twig_get_attribute($this->env, $this->source, $context["bundle"], "description", [], "any", false, false, true, 44)) {
                    // line 45
                    echo "            <div class=\"admin-item__description\" id=\"";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["description_id"] ?? null), 45, $this->source), "html", null, true);
                    echo "\">";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["bundle"], "description", [], "any", false, false, true, 45), 45, $this->source), "html", null, true);
                    echo "</div>
          ";
                }
                // line 47
                echo "        </div>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['bundle'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 49
            echo "    </div>
  </div>
";
        } elseif ( !twig_test_empty(        // line 51
($context["add_bundle_message"] ?? null))) {
            // line 52
            echo "  <p>
    ";
            // line 53
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["add_bundle_message"] ?? null), 53, $this->source), "html", null, true);
            echo "
  </p>
";
        }
    }

    public function getTemplateName()
    {
        return "themes/gin/templates/admin/entity-add-list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  127 => 53,  124 => 52,  122 => 51,  118 => 49,  111 => 47,  103 => 45,  100 => 44,  95 => 41,  87 => 40,  81 => 39,  76 => 38,  73 => 37,  70 => 36,  67 => 35,  65 => 31,  61 => 30,  57 => 29,  54 => 28,  48 => 26,  46 => 25,  43 => 24,  41 => 23,  39 => 19,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/gin/templates/admin/entity-add-list.html.twig", "/home/mims/project/web/themes/gin/templates/admin/entity-add-list.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 19, "if" => 23, "for" => 30);
        static $filters = array("escape" => 26, "clean_id" => 37);
        static $functions = array("create_attribute" => 36);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'for'],
                ['escape', 'clean_id'],
                ['create_attribute']
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
