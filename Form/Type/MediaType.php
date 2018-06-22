<?php

namespace AppVerk\MediaBundle\Form\Type;

use AppVerk\MediaBundle\Form\DataTransformer\MediaTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class MediaType extends AbstractType
{
    /**
     * @var MediaTransformer
     */
    private $mediaTransformer;

    public function __construct(MediaTransformer $mediaTransformer)
    {
        $this->mediaTransformer = $mediaTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->mediaTransformer);
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
