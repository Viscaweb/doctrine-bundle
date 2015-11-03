# Doctrine Bundle

## Configuration

Base config:

```
visca_doctrine:
    kernel: kernel
    manager_registry: doctrine
    naming:
        classes: visca_doctrine.naming.classes.default
        constants: visca_doctrine.naming.constant.default
    templating:
        engine: templating.engine.twig
    generator:
        unique_values_class:
            template: "ViscaDoctrineBundle::class.php.twig"
    caching:
        entities:
            -   class: %fos_comment.model.thread.class%
                strategy: visca_doctrine.cache.one_minute
```
