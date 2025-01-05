<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shippingAddress', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 3],
                'label' => 'Shipping Address'
            ])
            ->add('billingAddress', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 3],
                'label' => 'Billing Address'
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'choices' => [
                    'Credit Card' => 'credit_card',
                    'PayPal' => 'paypal',
                    'Bank Transfer' => 'bank_transfer'
                ],
                'attr' => ['class' => 'form-control'],
                'label' => 'Payment Method'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
} 