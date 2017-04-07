# Principle

The general id behind this bundle is to offer an flexible 
base implementation to help in the transformation of data coming
from multiple source into Victoire Blog.

It's based on an php implementation of the pattern design known has 
pipeline. You can find this implementation [there](https://github.com/thephpleague/pipeline)
 
A pipeline is a class implementing the following interface:

    interface PipelineInterface extends StageInterface
    {
        /**
         * Create a new pipeline with an appended stage.
         *
         * @param callable $operation
         *
         * @return static
         */
        public function pipe(callable $operation);
    
        /**
         * Execute the processor process method
         *
         * @param $payload
         *
         * @return mixed
         */
        public function process($payload);
    }
    
When a new Pipeline is instantiate it require a Processor. The processor is 
the class in charge of executing the pipeline stages chain. Basically it's just 
a loop who exec any callable method in the stages, passing an immutable object 
called a Payload from stage to stage.

A processor is build based on the following interface

    interface ProcessorInterface
    {
        /**
         * @param array $stages
         * @param $payload
         *
         * @return mixed
         */
        public function process(array $stages, $payload);
    }
    
The Stages is where the business is done. It consist in a simple class using any
callable method you want, who receive a payload in param and return it when is job is done.

Is create by implementing the following interface

    interface StageInterface
    {
        /**
         * @param CommandPayloadInterface $payload
         *
         * @return $payload
         */
        public function __invoke(CommandPayloadInterface $payload);
    }
    
So once I have some stage a pipeline an a processor we can build an execution chain

    $pipeline = new Pipeline(new Processor());
    
    $pipeline
        ->pipe(new Stage1)
        ->pipe(new Stage2)
        ->pipe(new Stage3)
    ->process()
    
    