








    <div class="block" id="mymodule_block_home">

    <h4>{l s='welcom!' mod='mymodule'}</h4>

    <div class="block_contact">
        <p>
            Hello ,
            {if isset($test) && $test}
                {$test}
            {else}
                world
            {/if}
            !
        </p>
        <ul>
            <li><a href="{$my_module_link}" title="click this link">Click Me!</a></li>
        </ul>
    </div>
</div>
